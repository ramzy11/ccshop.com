<?php

class Gri_Paypal_Model_Ipn  extends Mage_Paypal_Model_Ipn
{

    protected function _postBack(Zend_Http_Client_Adapter_Interface $httpAdapter)
    {
        /* @var Varien_Http_Adapter_Curl $httpAdapter */
        $sReq = '';
        foreach ($this->_request as $k => $v) {
            $sReq .= '&' . $k . '=' . urlencode($v);
        }
        $sReq .= "&cmd=_notify-validate";
        $sReq = substr($sReq, 1);
        $this->_debugData['postback'] = $sReq;
        $this->_debugData['postback_to'] = $this->_config->getPaypalUrl();

        $httpAdapter->addOption(CURLOPT_HTTPHEADER, array('Expect:'));
        $httpAdapter->write(Zend_Http_Client::POST, $this->_config->getPaypalUrl(), '1.1', array(), $sReq);
        try {
            $response = $httpAdapter->read();
        } catch (Exception $e) {
            $this->_debugData['http_error'] = array('error' => $e->getMessage(), 'code' => $e->getCode());
            throw $e;
        }
        $this->_debugData['postback_result'] = $response;

        $response = preg_split('/^\r?$/m', $response);
        $response = trim(end($response));
        if ($response != 'VERIFIED') {
            throw new Exception('PayPal IPN postback failure. See ' . self::DEFAULT_LOG_FILE . ' for details.');
        }
        unset($this->_debugData['postback'], $this->_debugData['postback_result']);
    }

    protected function _registerPaymentCapture()
    {
        if ($this->getRequestData('transaction_entity') == 'auth') {
            return;
        }
        /* @var Mage_Sales_Model_Order_Invoice $invoice */
        foreach ($this->_order->getInvoiceCollection() as $invoice) {
            if ($invoice->getState() == $invoice::STATE_PAID) return;
        }
        $this->_importPaymentInformation();
        $payment = $this->_order->getPayment();
        $payment->setTransactionId($this->getRequestData('txn_id'))
            ->setPreparedMessage($this->_createIpnComment(''))
            ->setParentTransactionId($this->getRequestData('parent_txn_id'))
            ->setShouldCloseParentTransaction('Completed' === $this->getRequestData('auth_status'))
            ->setIsTransactionClosed(0)
            ->registerCaptureNotification($this->getRequestData('mc_gross'));
        $this->_order->save();

        // notify customer
        if (($invoice = $payment->getCreatedInvoice()) && !$this->_order->getEmailSent()) {
            $comment = $this->_order->sendNewOrderEmail()->addStatusHistoryComment(
                Mage::helper('paypal')->__('Notified customer about invoice #%s.', $invoice->getIncrementId())
            )
                ->setIsCustomerNotified(true)
                ->save();
        }
    }

    protected function _registerPaymentRefund()
    {
        $this->_importPaymentInformation();
        $reason = $this->getRequestData('reason_code');
        $isRefundFinal = !$this->_info->isReversalDisputable($reason);
        $payment = $this->_order->getPayment();
        $payment->setPreparedMessage($this->_createIpnComment($this->_info->explainReasonCode($reason)))
            ->setTransactionId($txnId = $this->getRequestData('txn_id'))
            ->setParentTransactionId($this->getRequestData('parent_txn_id'))
            ->setIsTransactionClosed($isRefundFinal);
        if (!$payment->lookupTransaction($txnId)) {
            $amount = -1 * $this->getRequestData('mc_gross');
            $payment->addTransaction(Mage_Sales_Model_Order_Payment_Transaction::TYPE_REFUND, NULL, FALSE, Mage::helper('sales')->__('Registered notification about refunded amount of %s.', $this->_order->getBaseCurrency()->formatTxt($amount)));
        }
        $this->_order->save();
    }

    public function postBack(){
        $httpAdapter = new Varien_Http_Adapter_Curl();
        $this->_postBack($httpAdapter);
    }
}
