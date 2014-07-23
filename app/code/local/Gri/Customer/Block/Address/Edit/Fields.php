<?php

class Gri_Customer_Block_Address_Edit_Fields extends Mage_Core_Block_Template
{
    protected $_addressHighToLow = array(
        'zh_CN' => TRUE,
        'zh_HK' => TRUE,
        'zh_TW' => TRUE,
        'ja_JP' => TRUE,
    );

    protected function _construct()
    {
        parent::_construct();
        $this->getTemplate() or $this->setTemplate('customer/address/edit/fields.phtml');
        $this->getQualifySeparator() or $this->setQualifySeparator(':');
    }

    /**
     * Return Sales Quote Address model
     *
     * @return Mage_Sales_Model_Quote_Address|Mage_Customer_Model_Address
     */
    public function getAddress()
    {
        return $this->getParentBlock()->getAddress();
    }

    public function getCountryHtmlSelect($type = NULL)
    {
        return $this->getParentBlock()->getCountryHtmlSelect($type);
    }

    public function getFullyQualifiedId($id)
    {
        if (!$this->getAddressType()) return $id;
        return $this->getAddressType() . $this->getQualifySeparator() . $id;
    }

    public function getFullyQualifiedName($name)
    {
        if (!$this->getAddressType()) return $name;
        if ($bracketPosition = strpos($name, '[')) $name = substr(substr_replace($name, ']', $bracketPosition, 0), 0, -1);
        return $this->getAddressType() . '[' . $name . ']';
    }

    /**
     * Retrieve sales quote model
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->getParentBlock()->getQuote();
    }

    public function isAddressHighToLow()
    {
        return !empty($this->_addressHighToLow[Mage::app()->getLocale()->getLocaleCode()]);
    }
}
