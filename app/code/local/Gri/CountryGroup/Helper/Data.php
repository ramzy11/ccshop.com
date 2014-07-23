<?php
class Gri_CountryGroup_Helper_Data extends Mage_Core_Helper_Abstract
{

    /**
     * return array
     */
    public function getAllRegions()
    {
        $cache = Mage::getSingleton('core/cache');
        if ($cache->load("allRegions") == false) {
            $regions = array();
            $allCountries = Mage::getResourceModel('directory/country_collection')->loadData()->toOptionArray(false);
            foreach ($allCountries as $v) {
                $regions[$v['value']] = $v['label'];
            }
            // restore cache in 60 days
            $cache->save(serialize($regions), "allRegions", array("allRegions"), 60 * 60 * 24 * 60);
        }
        return unserialize($cache->load("allRegions"));
    }

    public function getProductCountries(Mage_Catalog_Model_Product $product)
    {
        if ($product->getCountries() === NULL) {
            $countries = explode(',', (string)Mage::getStoreConfig('general/country/allow'));
            if ($countryGroup = $product->getAttributeText('country_group')) {
                $countries = explode(',', Mage::getModel('gri_countrygroup/countrygroup')->load($countryGroup, 'name')->getValue());
            }
            if ($countries) {
                $countries = $this->convertCodeToRegions($countries);
            }
            $product->setCountries($countries);
        }
        return $product->getCountries();
    }

    /**
     * @param array $code
     * @return string|boolean
     */
    public function convertCodeToRegions($code)
    {
        $allRegions = $this->getAllRegions();
        if (!is_array($code) && isset($allRegions[$code])) return $allRegions[$code];
        if (is_array($code)) {
            $data = array();
            foreach ($code as $v) {
                if (isset($allRegions[$v])) $data[] = $allRegions[$v];
            }
            if ($data) return $data;
        }

        return false;
    }
}
