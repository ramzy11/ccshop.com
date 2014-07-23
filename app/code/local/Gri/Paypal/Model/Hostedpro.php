<?php


class Gri_Paypal_Model_Hostedpro extends Mage_Paypal_Model_Hostedpro
{
    /**
     * Sends API request to PayPal to get form URL, then sets this URL to $payment object.
     *
     * @param Mage_Payment_Model_Info $payment
     */
    protected function _setPaymentFormUrl(Mage_Payment_Model_Info $payment)
    {
        $request = $this->_buildFormUrlRequest($payment);
        Mage::log('Request: '.var_export($request->getRequestData(),true), 7, 'payPal-Hostpro.log');
        $response = $this->_sendFormUrlRequest($request);
        Mage::log('Response: '.var_export($response,true), 7, 'payPal-Hostpro.log');
        if ($response) {
            $payment->setAdditionalInformation('secure_form_url', $response);
        } else {
            Mage::throwException('Cannot get secure form URL from PayPal');
        }
    }
}
