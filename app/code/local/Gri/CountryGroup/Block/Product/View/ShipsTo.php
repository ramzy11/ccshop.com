<?php

class Gri_CountryGroup_Block_Product_View_ShipsTo extends Mage_Core_Block_Template
{
    const CONFIG_PATH_SHOW_SHIPS_TO = 'catalog/frontend/show_ships_to';

    protected function _toHtml()
    {
        if (!Mage::getStoreConfig(self::CONFIG_PATH_SHOW_SHIPS_TO)) return '';
        return parent::_toHtml();
    }

    public function getCountries()
    {
        if (($product = Mage::registry('current_product')) instanceof Mage_Catalog_Model_Product) {
            return $this->getCountryGroupHelper()->getProductCountries($product);
        }
        return array();
    }

    /**
     * @return Gri_CountryGroup_Helper_Data
     */
    public function getCountryGroupHelper()
    {
        return Mage::helper('gri_countrygroup');
    }
}
