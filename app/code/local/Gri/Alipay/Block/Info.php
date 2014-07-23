<?php

class Gri_Alipay_Block_Info extends Mage_Payment_Block_Info
{

    public function getSpecificInformation()
    {
        $orderId = Mage::app()->getRequest()->getParam('order_id');

        if ($orderId) {
            $order = Mage::getModel('sales/order')->load($orderId);
            $payBank = $order->getAlipayPayBank();
        } else {
            $quote = Mage::getSingleton('checkout/session')->getQuote();
            $payBank = $quote->getAlipayPayBank();
        }

        if (!empty($payBank)) {
            $prefix = $payBank == 'ALIPAY' ? '' : Mage::helper('alipay')->__('Online Bank');
            return array($prefix => $this->getBankName($payBank));
        }
    }

    public function getBankName($code)
    {
        return Mage::getSingleton('alipay/source_bank')->getLabel($code);
    }
}
