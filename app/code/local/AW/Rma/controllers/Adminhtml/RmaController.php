<?php
/**
* aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Rma
 * @version    1.3.1
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */


require_once 'AbstractController.php';

class AW_Rma_Adminhtml_RmaController extends AW_Rma_Adminhtml_AbstractController {

    protected function _initAction() {
        return $this->loadLayout()->_setActiveMenu('sales/awrma');
    }

    private function hasErrors() {
        return (bool) count($this->_getSession()->getMessages()->getItemsByType('error'));
    }

    public function saveAction() {
        /* @var $helper AW_Rma_Helper_Data */
        $helper = Mage::helper('awrma');
        $listType = $this->_getSession()->getData('awrma-list-type');
        if (!$this->_validateFormKey())
            return $this->_redirect('*/*/' . ($listType ? $listType : 'list'));

        $_data = array();
        $rmaRequest = Mage::getModel('awrma/entity');
        if ($this->getRequest()->getParam('id')) {
            $rmaRequest->load($this->getRequest()->getParam('id'));
        } else {
            $_data = $this->_getSession()->getAWRMAFormData();
            $rmaRequest->setData($_data);
        }
        $_data = $rmaRequest->getData();

        if(!isset($_data['order_entity_id']))
        {
            $order = Mage::getModel('sales/order')->load($_data['order_id'],'increment_id');
            $_data['order_entity_id'] = $order->getId();
        }

        $_data['status'] = $this->getRequest()->getParam('status');
        // Request type won't be changed
        $_data['request_type'] = $rmaRequest->getId() ?
            $rmaRequest->getRequestType() :
            $this->getRequest()->getParam('request_type')
        ;
        $_data['tracking_code'] = $this->getRequest()->getParam('tracking_code');
        $_data['admin_notes'] = $this->getRequest()->getParam('admin_notes');
        $_data['package_opened'] = $this->getRequest()->getParam('package_opened');
        $_data['exchange_items'] = $this->getRequest()->getParam('exchange_items');

        if (Mage::helper('awrma/config')->getAllowPerOrderRMA($rmaRequest->getStoreId()))
            $_data['order_items'] = $this->getRequest()->getParam('orderitems');
        else
            $_data['order_items'] = $rmaRequest->getOrderItems();

        /* @var $_order Mage_Sales_Model_Order */
        $_order = Mage::getModel('sales/order')->loadByIncrementId($_data['order_id']);
        $_orderItems = $_order->getItemsCollection();
        foreach ($_orderItems as $_item) {
            if ($_item->getData('product_type') == 'bundle' && $_item->getChildrenItems()) {
                $_data['order_items'][$_item->getData('item_id')] = 0;
                foreach ($_item->getChildrenItems() as $bundle_item) {
                    if (isset($_data['order_items'][$bundle_item->getData('item_id')])) {
                        $_data['order_items'][$_item->getData('item_id')] += $_data['order_items'][$bundle_item->getData('item_id')];
                    }
                }
            }
        }

        foreach ($_data['order_items'] as $k => $v)
            if ($_data['order_items'][$k] == 0)
                unset($_data['order_items'][$k]);

            if ($_data['order_items'] == array())
            $this->_getSession()->addError($helper->__('Request should have at least one item'));

        $subtractInventory = array();
        // Process new SKU for exchange
        if ($rmaRequest->getRequestType() == 1) {
            foreach ($_data['order_items'] as $k => $v) {
                $sku = isset($_data['exchange_items'][$k]) && $_data['exchange_items'][$k] ?
                    $_data['exchange_items'][$k] :
                    $_order->getItemById($k)->getSku();
                if ($productId = $helper->checkSku($sku)) $subtractInventory[$productId] = $v;
                else $this->_getSession()->addError($helper->__('New SKU "%s" not found', $sku));
            }
            // Only subtract inventory when first approval
            $rmaRequest->getStatus() != 1 || $_data['status'] != 2 and $subtractInventory = array();
        }

        if ($rmaRequest->getData('status') != $_data['status']
                && $_data['status'] == Mage::helper('awrma/status')->getApprovedStatusId()
                && !$rmaRequest->getApprovementCode())
            $_data['approvement_code'] = Mage::helper('awrma/request')->getApprovementCode();

        $printLabel = $this->getRequest()->getParam('printlabel');
        if (isset($printLabel['stateprovince']) && filter_var($printLabel['stateprovince'], FILTER_VALIDATE_INT)) {
            $printLabel['stateprovince_id'] = $printLabel['stateprovince'];
            $printLabel['stateprovince'] = $helper->getRegionName($printLabel['stateprovince_id']);
        }

        $_data['print_label'] = $printLabel;
        if (!$this->hasErrors()) {
            $rmaRequest->setData($_data);
            $rmaRequest->save();
            $this->_getSession()->addSuccess($helper->__('RMA successfully saved'));
            $helper->subtractInventory($subtractInventory);

            $_notified = FALSE;

            if ($this->getRequest()->getParam('comment_text')) {
                $_data = array();
                $_data['text'] = $this->getRequest()->getParam('comment_text');
                if ($_data['text']) {
                    if (isset($_FILES['comment_file']['name']) && $_FILES['comment_file']['name']) {
                        if (!in_array(Mage::helper('awrma/files')->getExtension($_FILES['comment_file']['name']), Mage::helper('awrma/config')->getForbiddenExtensions())) {
                            if ($_FILES['comment_file']['size'] <= Mage::helper('awrma/config')->getMaxAttachmentsSize() && $_FILES['comment_file']['size'] > 0) {
                                if ($_FILES['comment_file']['error'] == UPLOAD_ERR_OK) {
                                    try {
                                        $uploader = new Varien_File_Uploader('comment_file');
                                        $uploader
                                                ->setAllowedExtensions(null)
                                                ->setAllowRenameFiles(TRUE)
                                                ->setAllowCreateFolders(TRUE)
                                                ->setFilesDispersion(FALSE);
                                        $result = $uploader->save(Mage::helper('awrma/files')->getPath(), $_FILES['comment_file']['name']);
                                        $_data['attachments'] = $result['file'];
                                    } catch (Exception $ex) {
                                        $this->_getSession()->addError($ex->getMessage());
                                    }
                                } else {
                                    $this->_getSession()->addError($helper->__('Some error occurs when uploading file'));
                                }
                            } else {
                                $this->_getSession()->addError($helper->__('Maximal allowed attachment size is ' . (floor(Mage::helper('awrma/config')->getMaxAttachmentsSize() / 1024)) . ' kb'));
                            }
                        } else {
                            $this->_getSession()->addError($helper->__('Forbidden file extension'));
                        }
                    }

                    if (!$this->hasErrors()) {
                        $_data['owner'] = AW_Rma_Model_Source_Owner::ADMIN;
                        Mage::helper('awrma/comments')->postComment($rmaRequest->getId(), $_data['text'], $_data, FALSE);
                        $this->_getSession()->addSuccess($helper->__('Comment successfully added'));
                        Mage::getModel('awrma/notify')->checkChanges($rmaRequest, $_data['text']);
                        $_notified = TRUE;
                    }
                } else {
                    $this->_getSession()->addError($helper->__('Comment text can\'t be empty'));
                }
            }
        }

        if ($this->hasErrors()) {
            return $this->_redirect('*/*/edit', array('id' => $rmaRequest->getId()));
        } else {
            if (!$_notified) {
                Mage::getModel('awrma/notify')->checkChanges($rmaRequest);
            }
            if ($this->getRequest()->getParam('continue')) {
                return $this->_redirect('*/*/edit', array('id' => $rmaRequest->getId()));
            } elseif ($this->getRequest()->getParam('print')) {
                $rmaRequest->load($rmaRequest->getId());
                return $this->_redirect('*/*/edit', array('id' => $rmaRequest->getId(), 'printstore' => $rmaRequest->getStoreId(), 'printurl' => $rmaRequest->getExternalLink()));
            } else {
                return $this->_redirect('*/*/' . ($listType ? $listType : 'list'));
            }
        }
    }

    protected function indexAction() {
        $listType = $this->_getSession()->getData('awrma-list-type');
        if ($listType)
            return $this->_redirect('*/*/' . $listType);
        else
            return $this->_redirect('*/*/list');
    }

    protected function editAction() {

        if ($this->getRequest()->getParam('id')) {
            $this->_setTitle('Sales')
                    ->_setTitle('RMA')
                    ->_setTitle('Edit RMA');
            $this->_initAction();

            $_rmaRequest = Mage::getModel('awrma/entity')->load($this->getRequest()->getParam('id'));
            if ($_rmaRequest->getData() != array()) {
                Mage::register('awrmaformdatarma', $_rmaRequest, TRUE);
                $this->_addContent($this->getLayout()->createBlock('awrma/adminhtml_rma_edit', 'rma_edit'))
                        ->_addLeft($this->getLayout()->createBlock('awrma/adminhtml_rma_edit_tabs'));
            } else {
                $this->_getSession()->addError($this->__('Can\'t load RMA by given ID'));
            }
        } else {
            $this->_setTitle('Sales')
                    ->_setTitle('RMA')
                    ->_setTitle('New RMA');
            $this->_initAction();

            $_data = $this->_getSession()->getAWRMAFormData();
            if ($_data) {
                $_rmaRequest = Mage::getModel('awrma/entity')->setData($_data);
                Mage::register('awrmaformdatarma', $_rmaRequest, TRUE);
                $this->_addContent($this->getLayout()->createBlock('awrma/adminhtml_rma_edit', 'rma_edit'))
                        ->_addLeft($this->getLayout()->createBlock('awrma/adminhtml_rma_edit_tabs'));
            } else {
                $this->_getSession()->addError($this->__('RMA id isn\'t specified'));
            }
        }
        if ($editBlock = $this->getLayout()->getBlock('rma_edit')) {
            /* @var $resetApiCounterBlock Gri_Api_Block_Adminhtml_Edit_ResetApiRetryCounter */
            $resetApiCounterBlock = $this->getLayout()->createBlock('gri_api/adminhtml_edit_resetApiRetryCounter',
                'reset.api.retry.counter')->setObjectName('RmaRequest');
            $editBlock->setChild('reset.api.retry.counter', $resetApiCounterBlock);
            $resetApiCounterBlock->addResetButton();
        }
        if ($this->hasErrors()) {
            $listType = $this->_getSession()->getData('awrma-list-type');
            if ($listType)
                return $this->_redirect('*/*/' . $listType);
            else
                return $this->_redirect('*/*/list');
        }
        $this->renderLayout();
    }

    protected function listpendingAction() {
        $this->_setTitle('Sales')
                ->_setTitle('RMA')
                ->_setTitle('Pending Requests');
        $this->_getSession()->setData('awrma-list-type', 'listpending');
        $this->_initAction()->renderLayout();
    }

    protected function listAction() {
        $this->_setTitle('Sales')
                ->_setTitle('RMA')
                ->_setTitle('All RMA');
        $this->_getSession()->setData('awrma-list-type', 'list');
        $this->_initAction()->renderLayout();
    }

    protected function downloadAction() {
        if ($this->getRequest()->getParam('cid')) {
            $_comment = Mage::getModel('awrma/entitycomments')->load($this->getRequest()->getParam('cid'));
            if ($_comment->getData() != array()) {
                return Mage::helper('awrma/files')->downloadFile($_comment->getAttachments());
            } else {
                $this->_getSession()->addError($this->__('Can\'t load comment'));
            }
        } else {
            $this->_getSession()->addError($this->__('Comment ID isn\'t specified'));
        }
        $this->_redirect('awrma/adminhtml_rma/index');
    }

    protected function _isAllowed() {
        $_action = $this->getRequest()->getActionName();
        $_id = $this->getRequest()->getParam('id');
        $commonActions = array(
            'ordergrid' => 1,
            'index' => 1,
            'download' => 1,
        );
        if (!$_action || isset($commonActions[$_action])) {
            return Mage::getSingleton('admin/session')->isAllowed('sales/awrma/list');
        }

        if (($_action == 'save' || $_action == 'edit') && $_id) {
            return Mage::getSingleton('admin/session')->isAllowed('sales/awrma/edit');
        }

        if (($_action == 'save' || $_action == 'edit') && !$_id) {
            return Mage::getSingleton('admin/session')->isAllowed('sales/awrma/createrequest');
        }
        return Mage::getSingleton('admin/session')->isAllowed('sales/awrma/' . $_action);
    }

    public function newAction() {
        $this->_setTitle('Sales')
                ->_setTitle('RMA')
                ->_setTitle('New RMA');
        $this->_initAction();
        $this->_addContent($this->getLayout()->createBlock('awrma/adminhtml_rma_new'))
                ->_addContent($this->getLayout()->createBlock('awrma/adminhtml_rma_new_form'))
                ->_addLeft($this->getLayout()->createBlock('awrma/adminhtml_rma_new_tabs'));
        $this->renderLayout();
    }

    public function ordersuggestAction() {
        $ordersLimit = 50;
        $text = $this->getRequest()->getParam('text');
        $orders = Mage::getModel('sales/order')->getCollection();
        $suggest = array();
        if ($text) {
            $orders->getSelect()
                    ->where('increment_id LIKE ?', '%' . $text . '%')
                    ->limit($ordersLimit)
            ;
            if ($orders->getSize() > $ordersLimit) {
                $suggest[] = array(
                    'value' => '',
                    'text' => Mage::helper('awrma')->__('Too many orders found. Check your request')
                );
            } else {
                foreach ($orders->getItems() as $item) {
                    $suggest[] = array(
                        'value' => $item->getIncrementId(),
                        'text' => $item->getIncrementId(),
                    );
                }
            }
        }
        return $this->getResponse()->setBody(Zend_Json::encode($suggest));
    }

    public function createrequestAction() {
        $session = Mage::getSingleton('adminhtml/session');
        $request = $this->getRequest();
        $_data = array();
        $_addNewEntityFlag = TRUE;

        $_data['order_id'] = $request->getParam('order');
        //Checking OrderID
        if ($_data['order_id']) {
            //Trying to load order
            /* @var $_order Gri_Sales_Model_Order */
            $_order = Mage::getModel('sales/order')->loadByIncrementId($_data['order_id']);
            if (!($_order->getData() == array())) {
                //Gets order items from post if per-order item RMA is allowed
                //and gets it directly from order otherwise
                foreach ($_order->getItemsCollection() as $_item) {
                        $_orderItems[$_item->getId()] = Mage::helper('awrma')->getItemMaxCount($_item);
                }

                if ($_addNewEntityFlag) {
                    $_data['order_items'] = $_orderItems;
                    //Checking package opened and request type values

                    if (!(Mage::getModel('awrma/source_packageopened')->getOption($request->getParam('packageopened')) === FALSE)) {

                        $_data['package_opened'] = $request->getParam('packageopened');
                        $_data['request_type'] = $request->getParam('requesttype') ? $request->getParam('requesttype') : null;

                        $_data['created_at'] = date(AW_Rma_Model_Mysql4_Entity::DATETIMEFORMAT, time());
                        $_data['status'] = Mage::helper('awrma/status')->getPendingApprovalStatusId();
                        $_data['external_link'] = Mage::helper('awrma')->getExtLink();

                        $_data['customer_email'] = $_order->getCustomerEmail();
                        $_data['customer_name'] = $_order->getCustomerName();
                        $_data['customer_id'] = $_order->getCustomerId();

                        $session->setAWRMAFormData($_data);
                        return $this->_redirect('*/*/edit', array());
                    } else {
                        $_addNewEntityFlag = FALSE;
                        $session->addError(Mage::helper('awrma')->__('Wrong form data'));
                    }
                }
            } else {
                $_addNewEntityFlag = FALSE;
                $session->addError(Mage::helper('awrma')->__('Wrong order ID'));
            }
        } else {
            $_addNewEntityFlag = FALSE;
            $session->addError(Mage::helper('awrma')->__('Wrong form data'));
        }
        return $this->_redirect('*/*/new');
    }

    protected function gridAction() {
        $this->loadLayout();
        $this->getResponse()->setBody($this->getLayout()->createBlock('awrma/adminhtml_rma_grid')->toHtml());
    }

    protected function ordergridAction() {
        $this->loadLayout();
        $this->getResponse()->setBody($this->getLayout()->createBlock('awrma/adminhtml_sales_order_view_tabs_requests_grid')->toHtml());
    }

}
