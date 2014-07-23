<?php

class Gri_Alipay_Model_Observer extends Varien_Object
{

    public function setBank(Varien_Event_Observer $observer)
    {
        /* @var $payment Mage_Sales_Model_Quote_Payment */
        $payment = $observer->getEvent()->getPayment();
        $data = $observer->getEvent()->getInput();
        $quote = $payment->getQuote();

        if(isset($data['alipay_pay_bank']) && $data['alipay_pay_bank']) {
            $quote->setAlipayPayMethod($data['alipay_pay_method']);
            $quote->setAlipayPayBank($data['alipay_pay_bank']);
        }
    }
}
