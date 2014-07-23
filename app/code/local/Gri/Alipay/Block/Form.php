<?php

class Gri_Alipay_Block_Form extends Mage_Payment_Block_Form
{
    protected function _construct()
    {
        $this->setTemplate('alipay/form.phtml');
        parent::_construct();
    }

    /**
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return Mage::getSingleton('checkout/session')->getQuote();
    }

    public function getBanks()
    {
        return Mage::getSingleton('alipay/source_bank')->toOptionArray();
    }

    /**
     * Retrieve code of current payment method
     *
     * @return string
     */
    public function getSelectedBankCode()
    {
        if ($bank = $this->getQuote()->getPayment()->getAlipayPayBank()) {
            return $bank;
        }
        return 'ALIPAY';
    }

    /**
     * Retrieve code of current payment method
     *
     * @return string
     */
    public function getSelectedMethodCode()
    {
        if ($method = $this->getQuote()->getPayment()->getMethod()) {
            return $method;
        }
        return 'alipay_payment';
    }
}
