<?php

class Gri_Core_Model_Locale extends Mage_Core_Model_Locale
{
    protected $_specialCountries = array('CN', 'HK', 'MO');

    public function getCountryTranslation($value)
    {
        if (substr($this->getLocaleCode(), 0, 2) == 'zh' &&
            in_array($value, $this->_specialCountries)
        ) {
            return Mage::helper('gri_core')->__($value);
        }
        return parent::getCountryTranslation($value);
    }
}
