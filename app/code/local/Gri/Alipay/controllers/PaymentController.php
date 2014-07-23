<?php

class Gri_Alipay_PaymentController extends Mage_Core_Controller_Front_Action
{
    protected $_order;

    public function getOrder()
    {
        if ($this->_order == null) {
            $session = Mage::getSingleton('checkout/session');
            $this->_order = Mage::getModel('sales/order');
            $this->_order->loadByIncrementId($session->getLastRealOrderId());
        }
        return $this->_order;
    }

    public function redirectAction()
    {
        $session = Mage::getSingleton('checkout/session');
        $session->setAlipayPaymentQuoteId($session->getQuoteId());
        if ($incrementId = $this->getRequest()->getParam('increment_id')) {
            $session->setLastRealOrderId($incrementId);
        }

        /*$order = $this->getOrder();
		if (!$order->getId()) {
			$this->norouteAction();
			return;
		}

		$order->addStatusToHistory(
			$order->getStatus(),
			Mage::helper('alipay')->__('Customer was redirected to Alipay')
		);
		$order->save();*/

        $this->getResponse()->setBody($this->getLayout()
            ->createBlock('alipay/redirect')
            ->toHtml());
        $session->unsQuoteId();
    }

    public function successAction()
    {
        $session = Mage::getSingleton('checkout/session');
        $session->setQuoteId($session->getAlipayPaymentQuoteId());
        $session->unsAlipayPaymentQuoteId();
        $order = $this->getOrder();
        try {
            $order->sendNewOrderEmail();
        } catch (Exception $e) {
            Mage::logException($e);
        }
        /*$order = $this->getOrder();
		if (!$order->getId()) {
			$this->norouteAction();
			return;
		}

		$order->addStatusToHistory($order->getStatus(), Mage::helper('alipay')->__('Customer successfully returned from Alipay'));
		$order->save();*/
        $this->_redirect('checkout/onepage/success');
    }

    public function confirmAction()
    {
        if (! $this->getRequest()->isPost()) {
            $this->_redirect('');
            return;
        }

        Mage::getSingleton('alipay/payment')->confirmPayment($this->getRequest()
            ->getPost());
    }

    public function testAction()
    {
        if (Gri_Alipay_Model_Payment::DEBUG) {
            $data = array('_input_charset' => 'utf-8', 'service' => 'notify_verify', 'partner' => '2088002377980031', 'notify_id' => 'a86946a370c5000791abed1d4f6a0f62');

            $query_string = http_build_query($data);
            //print_r($query_string);
            $http = new Varien_Http_Adapter_Curl();
            $http->write(Zend_Http_Client::GET, Gri_Alipay_Model_Payment::GATEWAY_URL . '?' . $query_string, '1.1', array());
            $response = $http->read();

            $response = preg_split('/^\r?$/m', $response, 2);
            $response = trim($response[1]);
            echo $response;

     //			$debug = Mage::getModel('xpaypal/api_debug');
        //			$debug->setRequestBody('request')->setResponseBody('response');
        //			$debug->save();
        }
    }

    public function notifyAction()
    {
        $model = Mage::getModel('alipay/payment');

        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost();
            $method = 'post';

        } else
            if ($this->getRequest()->isGet()) {
                $postData = $this->getRequest()->getQuery();
                $method = 'get';

            } else {
                $model->generateErrorResponse();
            }

        $order = Mage::getModel('sales/order')->loadByIncrementId($postData['reference']);

        if (! $order->getId()) {
            $model->generateErrorResponse();
        }

        if ($returnedMAC == $correctMAC) {
            if (1) {
                $order->addStatusToHistory($model->getConfigData('order_status_payment_accepted'), Mage::helper('alipay')->__('Payment accepted by Alipay'));

                $order->sendNewOrderEmail();

                if ($this->saveInvoice($order)) {
                    $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true);
                }

            } else {
                $order->addStatusToHistory($model->getConfigData('order_status_payment_refused'), Mage::helper('alipay')->__('Payment refused by Alipay'));

     // TODO: customer notification on payment failure
            }

            $order->save();

        } else {
            $order->addStatusToHistory(Mage_Sales_Model_Order::STATE_CANCELED, //$order->getStatus(),
Mage::helper('alipay')->__('Returned MAC is invalid. Order cancelled.'));
            $order->cancel();
            $order->save();
            $model->generateErrorResponse();
        }
    }

    /**
     * Save invoice for order
     *
     * @param    Mage_Sales_Model_Order $order
     * @return	  boolean Can save invoice or not
     */
    protected function saveInvoice(Mage_Sales_Model_Order $order)
    {
        if ($order->canInvoice()) {
            $convertor = Mage::getModel('sales/convert_order');
            $invoice = $convertor->toInvoice($order);
            foreach ($order->getAllItems() as $orderItem) {
                if (! $orderItem->getQtyToInvoice()) {
                    continue;
                }
                $item = $convertor->itemToInvoiceItem($orderItem);
                $item->setQty($orderItem->getQtyToInvoice());
                $invoice->addItem($item);
            }
            $invoice->collectTotals();
            $invoice->register()->capture();
            Mage::getModel('core/resource_transaction')->addObject($invoice)
                ->addObject($invoice->getOrder())
                ->save();
            return true;
        }

        return false;
    }

    /**
     * Failure payment page
     *
     * @param    none
     * @return	  void
     */
    public function errorAction()
    {
        $session = Mage::getSingleton('checkout/session');
        $errorMsg = Mage::helper('alipay')->__('There was an error occurred during paying process.');

        $order = $this->getOrder();

        if (! $order->getId()) {
            $this->norouteAction();
            return;
        }
        if ($order instanceof Mage_Sales_Model_Order && $order->getId()) {
            $order->addStatusToHistory(Mage_Sales_Model_Order::STATE_CANCELED, Mage::helper('alipay')->__('Customer returned from Alipay.') . $errorMsg);
            $order->save();
        }

        $this->loadLayout();
        $this->renderLayout();
        Mage::getSingleton('checkout/session')->unsLastRealOrderId();
    }
}
