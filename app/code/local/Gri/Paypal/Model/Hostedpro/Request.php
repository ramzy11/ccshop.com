<?php

class Gri_Paypal_Model_Hostedpro_Request extends Mage_Paypal_Model_Hostedpro_Request
{
    /**
     * Get peymet request data as array
     *
     * @param Mage_Paypal_Model_Hostedpro $paymentMethod
     * @return array
     */
    protected function _getPaymentData(Mage_Paypal_Model_Hostedpro $paymentMethod)
    {
        $request = array(
            'paymentaction' => strtolower($paymentMethod->getConfigData('payment_action')),
            'notify_url'    => $paymentMethod->getNotifyUrl(),
            'cancel_return' => $paymentMethod->getCancelUrl(),
            'return'        => $paymentMethod->getReturnUrl(),
            'lc'            => $this->_getStoreCountryCode(),  // $paymentMethod->getMerchantCountry(),

          //  'template'              => 'templateB',
            'showBillingAddress'    => 'false',
            'showShippingAddress'   => 'false',
            'showBillingEmail'      => 'false',
            'showBillingPhone'      => 'false',
            'showCustomerName'      => 'false',
            'showCardInfo'          => 'true',
            'showHostedThankyouPage'=> 'false'
        );

        return $request;
    }

    protected function _getStoreCountryCode()
    {
        return 1 == Mage::app()->getStore()->getId() ? 'HK':'US';
    }
}
