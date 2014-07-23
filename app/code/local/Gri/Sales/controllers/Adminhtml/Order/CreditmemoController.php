<?php
class_exists('Gri_Sales_Adminhtml_Order_CreditmemoController', FALSE) or
    include Mage::getModuleDir('controllers', 'Mage_Adminhtml') . '/Sales/Order/CreditmemoController.php';

class Gri_Sales_Adminhtml_Order_CreditmemoController extends Mage_Adminhtml_Sales_Order_CreditmemoController
{

    /**
     * @return Mage_Admin_Model_Session
     */
    protected function _getAdminSession()
    {
        return Mage::getSingleton('admin/session');
    }

    /**
     * @param bool $update
     * @return Gri_Sales_Model_Order_Creditmemo
     */
    protected function _initCreditmemo($update = FALSE)
    {
        if (!$creditmemo = Mage::registry('current_creditmemo')) {
            $creditmemo = parent::_initCreditmemo($update);
        }
        return $creditmemo;
    }

    protected function _redirect($path, $arguments = array())
    {
        if (substr($path, 0, 2) == '*/') {
            $adminFrontName = (string)Mage::getConfig()->getNode(Mage_Adminhtml_Helper_Data::XML_PATH_ADMINHTML_ROUTER_FRONTNAME);
            $path = $adminFrontName . '/' . substr($path, 2);
            Mage::app()->getFrontController()->getRouterByRoute('admin')->addModule($adminFrontName, 'Mage_Adminhtml', 'admin');
        }
        parent::_redirect($path, $arguments);
    }

    /**
     * @param Gri_Sales_Model_Order_Creditmemo $creditmemo
     * @return Gri_Sales_Adminhtml_Order_CreditmemoController
     */
    protected function _saveCreditmemo($creditmemo)
    {
        $creditmemo->getCreditmemoStatus() == $creditmemo::STATUS_ORDER_CANCELED and
            $creditmemo->setState($creditmemo::STATE_ORDER_CANCELED)->setCreditmemoStatus();
        $creditmemo->getCreditmemoStatus() == $creditmemo::STATUS_NOTIFIED and
            $creditmemo->setState($creditmemo::STATE_NOTIFIED)->setCreditmemoStatus();
        return parent::_saveCreditmemo($creditmemo);
    }

    public function addTransactionAction()
    {
        try {
            if (!$txnId = $this->getRequest()->getPost('txn_id')) {
                Mage::throwException($this->__('Transaction ID cannot be empty.'));
            }
            if ($creditmemo = $this->_initCreditmemo()) {
                $order = $creditmemo->getOrder();
                $isRefundFinal = !$order->canCreditmemo();
                $payment = $order->getPayment();
                $parentTxn = $order->getTransaction(Mage_Sales_Model_Order_Payment_Transaction::TYPE_CAPTURE);
                $parentTxn and $parentTxn = $parentTxn->getTxnId();
                $payment->setPreparedMessage($this->__('Added transaction ID %s', $txnId))
                    ->setTransactionId($txnId)
                    ->setParentTransactionId($parentTxn)
                    ->setIsTransactionClosed($isRefundFinal);
                if ($transaction = $payment->lookupTransaction($txnId)) {
                    if ($transaction->getTxnType() != $transaction::TYPE_REFUND) {
                        Mage::throwException($this->__('The type of transaction ID %s is not for refund.', $txnId));
                    }
                    $creditmemo->setTransactionId($txnId)->save();
                } else {
                    $amount = $creditmemo->getBaseMoneyTotalRefunded();
                    $payment->addTransaction(Mage_Sales_Model_Order_Payment_Transaction::TYPE_REFUND, $creditmemo, FALSE, Mage::helper('sales')->__('Registered notification about refunded amount of %s.', $order->getBaseCurrency()->formatTxt($amount)));
                    $order->addRelatedObject($creditmemo);
                    $order->save();
                }

                $this->loadLayout();
                $response = $this->getLayout()
                    ->createBlock('adminhtml/sales_order_payment', 'order_payment')
                    ->setParentBlock($this->getLayout()->createBlock('adminhtml/sales_order_creditmemo_view_form', 'form'))
                    ->toHtml();
            } else {
                $response = array(
                    'error' => TRUE,
                    'message' => $this->__('Cannot initialize creditmemo for adding transaction ID.'),
                );
            }
        } catch (Mage_Core_Exception $e) {
            $response = array(
                'error' => TRUE,
                'message' => $e->getMessage(),
            );
        } catch (Exception $e) {
            $response = array(
                'error' => TRUE,
                'message' => $this->__('Cannot add transaction ID.'),
            );
        }
        if (is_array($response)) {
            $response = Mage::helper('core')->jsonEncode($response);
        }
        $this->getResponse()->setBody($response);
    }

    public function cancelAction()
    {
        $creditmemo = $this->_initCreditmemo();
        if ($creditmemo) {
            try {
                $creditmemo->cancel();
                $creditmemo->addComment($this->__('Canceled by %s', $this->_getAdminSession()->getUser()->getUsername()),
                    FALSE, FALSE, 'cancel');
                $creditmemo->getNotified() or $creditmemo->setState($creditmemo::STATE_CANCELED_CLOSED);
                $this->_saveCreditmemo($creditmemo);
                $this->_getSession()->addSuccess($this->getSalesHelper()->__('The credit memo has been canceled.'));
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError($this->getSalesHelper()->__('Unable to cancel the credit memo.'));
            }
            $this->_redirect('*/sales_creditmemo/view', array('creditmemo_id'=>$creditmemo->getId()));
        } else {
            $this->_forward('noRoute');
        }
    }

    public function editAction()
    {
        if ($this->_initCreditmemo()->canUpdate()) {
            if ($creditmemo = $this->_initCreditmemo()) {
                Mage::register('edit', TRUE);
                $this->_title($this->__('Edit Memo'));

                if ($comment = Mage::getSingleton('adminhtml/session')->getCommentText(true)) {
                    $creditmemo->setCommentText($comment);
                }

                $this->loadLayout()
                    ->_setActiveMenu('sales/order')
                    ->renderLayout();
            } else {
                $this->_forward('noRoute');
            }
        }
        else {
            $this->_forward('view', 'sales_order_creditmemo', 'admin');
        }
    }

    /**
     * @return Gri_Sales_Helper_Creditmemo
     */
    public function getCreditmemoHelper()
    {
        return Mage::helper('gri_sales/creditmemo');
    }

    /**
     * @return Gri_Sales_Helper_Data
     */
    public function getGriHelper()
    {
        return Mage::helper('gri_sales');
    }

    /**
     * @return Mage_Sales_Helper_Data
     */
    public function getSalesHelper()
    {
        return Mage::helper('sales');
    }

    public function refundAction()
    {
        try {
            $creditmemo = $this->_initCreditmemo();
            $creditmemo->addComment($this->__('Refunded by %s', $this->_getAdminSession()->getUser()->getUsername()),
                FALSE,FALSE, 'refund');
            if ($creditmemo && $creditmemo->getId()) {
                $creditmemo->setRealRegister(TRUE);
                // Update status to canceled_and_refunded instead of closed for cancel and refund
                if ($creditmemo->getState() == $creditmemo::STATE_ORDER_CANCELED) {
                    $this->getGriHelper()->setDefaultOrderStatusForState('closed', 'canceled_and_refunded');
                }
                $creditmemo->register();
                $creditmemo->getOrder()->setCustomerNoteNotify(FALSE);
                $this->_saveCreditmemo($creditmemo);
                $this->_getSession()->addSuccess($this->__('The credit memo has been refunded.'));
                $this->_redirect('*/sales_order_creditmemo/view', array('creditmemo_id' => $creditmemo->getId()));
                return;
            } else {
                $this->_forward('noRoute');
                return;
            }
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_getSession()->addError($this->__('Cannot refund the credit memo.'));
        }
        $this->_redirect('*/sales_order_creditmemo/view', array('creditmemo_id' => $this->getRequest()->getParam('creditmemo_id')));
    }

    public function saveAction()
    {
        try {
            $creditmemo = $this->_initCreditmemo()
                ->setCanCreateOrder(TRUE)
                ->setCreditmemoStatus(Gri_Sales_Model_Order_Creditmemo::STATUS_ORDER_CANCELED);
        }
        catch (Exception $e) {
            Mage::logException($e);
            $this->_getSession()->addError($e->getMessage());
        }
        parent::saveAction();
        Mage::dispatchEvent('gri_order_cancellation', array('creditmemo' => $creditmemo));
    }

    public function updateAction()
    {
        Mage::register('update_creditmemo', TRUE);
        $creditmemo = $this->_initCreditmemo();
        if (!$creditmemo->getId()) {
            $this->_forward('noRoute');
            return;
        }

        // Prepare new creditmemo
        $data   = $this->getRequest()->getParam('creditmemo');
        $order  = $creditmemo->getOrder();
        $invoice = $this->_initInvoice($order);

        $savedData = $this->_getItemData();

        $qtys = array();
        $backToStock = array();
        foreach ($savedData as $orderItemId =>$itemData) {
            if (isset($itemData['qty'])) {
                $qtys[$orderItemId] = $itemData['qty'];
            }
            if (isset($itemData['back_to_stock'])) {
                $backToStock[$orderItemId] = true;
            }
        }
        $data['qtys'] = $qtys;

        try {
            /* @var $service Mage_Sales_Model_Service_Order */
            $service = Mage::getModel('sales/service_order', $order);
            if ($invoice) {
                $newCreditmemo = $service->prepareInvoiceCreditmemo($invoice, $data);
            } else {
                $newCreditmemo = $service->prepareCreditmemo($data);
            }

            $newCreditmemo->setState($creditmemo->getState())
                ->setCreditmemoStatus($creditmemo->getCreditmemoStatus())
                ->setCreatedAt($creditmemo->getCreatedAt());
            // Migrate new creditmemo data
            foreach ($newCreditmemo->getData() as $k => $v) {
                $creditmemo->setData($k, $v);
            }
            foreach ($newCreditmemo->getAllItems() as $item) {
                $item->setParentId($creditmemo->getId());
                if ($origItem = $creditmemo->getItemByOrderId($item->getOrderItemId()))
                    foreach ($item->getData() as $k => $v) $origItem->setData($k, $v);
                else $creditmemo->addItem($item);
            }

            // Original save action
            $data = $this->getRequest()->getPost('creditmemo');
            if (!empty($data['comment_text'])) {
                Mage::getSingleton('adminhtml/session')->setCommentText($data['comment_text']);
            }

            if (($creditmemo->getGrandTotal() <= 0) && (!$creditmemo->getAllowZeroGrandTotal())) {
                Mage::throwException(
                    $this->getSalesHelper()->__('Credit memo\'s total must be positive.')
                );
            }
            $creditmemo->addComment($this->__('Updated by %s', $this->_getAdminSession()->getUser()->getUsername()),
                FALSE, FALSE, 'update');

            $comment = '';
            if (!empty($data['comment_text'])) {
                $creditmemo->addComment(
                    $data['comment_text'],
                    isset($data['comment_customer_notify']),
                    isset($data['is_visible_on_front'])
                );
                if (isset($data['comment_customer_notify'])) {
                    $comment = $data['comment_text'];
                }
            }

            if (isset($data['do_refund'])) {
                $creditmemo->setRefundRequested(true);
            }
            if (isset($data['do_offline'])) {
                $creditmemo->setOfflineRequested((bool)(int)$data['do_offline']);
            }

            $creditmemo->register();
            if (!empty($data['send_email'])) {
                $creditmemo->setEmailSent(true);
            }
            $creditmemo->getNotified() and $creditmemo->setState($creditmemo::STATE_UPDATED);

            $creditmemo->getOrder()->setCustomerNoteNotify(!empty($data['send_email']));
            $this->_saveCreditmemo($creditmemo);
            $creditmemo->sendEmail(!empty($data['send_email']), $comment);
            $this->_getSession()->addSuccess($this->__('The credit memo has been updated.'));
            Mage::getSingleton('adminhtml/session')->getCommentText(true);
            $this->_redirect('*/sales_order_creditmemo/view', array('creditmemo_id' => $creditmemo->getId()));
            return;
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            Mage::getSingleton('adminhtml/session')->setFormData($data);
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_getSession()->addError($this->getSalesHelper()->__('Cannot save the credit memo.'));
        }
        $this->_redirectReferer();
    }

    public function updateQtyAction()
    {
        $this->_forward('updateQty', 'sales_order_creditmemo', 'admin');
    }
}
