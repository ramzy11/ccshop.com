<?php

class Gri_Alipay_Block_Redirect extends Mage_Core_Block_Abstract
{

    protected function _toHtml()
    {
        /* @var $standard Gri_Alipay_Model_Payment */
        $standard = Mage::getModel('alipay/payment');

        $form = new Varien_Data_Form();
        $form->setAction($standard->getGatewayUrl())
            ->setId('alipay_payment_checkout')
            ->setName('alipay_payment_checkout')
            ->setMethod('GET')
            ->setUseContainer(TRUE);

        foreach ($standard->getStandardCheckoutFormFields() as $k => $v) {
            $form->addField($k, 'hidden', array('name' => $k, 'value' => $v));
        }

        $html = '<html><body>';
        $html .= $this->__('You will be redirected to payment page in a few seconds.');
        $html .= $form->toHtml();
        $html .= '<script type="text/javascript">setTimeout(function(){document.getElementById("alipay_payment_checkout").submit();}, 1000);</script>';
        $html .= '</body></html>';

        return $html;
    }
}
