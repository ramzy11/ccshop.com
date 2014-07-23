<?php

class Gri_Directory_Helper_Data extends Mage_Directory_Helper_Data
{
    const CONFIG_PATH_OPTIONAL_CITY_COUNTRIES = 'general/country/optional_city_countries';

    protected $_cityLabels = array(
        'HK' => 'District',
        'MO' => 'District',
        'TH' => 'Amphoe',
        'TW' => 'District',
    );
    protected $_optionalCityCountries;
    protected $_regionLabels = array(
        'CN' => 'Province',
        'HK' => 'Area',
        'MO' => 'Area',
        'MY' => 'State',
        'SG' => 'City',
        'TH' => 'Changwat',
        'TW' => 'City',
    );

    protected $_internalCodes = array(
        'CN' => '86',
        'HK' => '852',
        'TW' => '886',
        'SG' => '65',
        'TH' => '66',
        'MY' => '60',
        'MO' => '853'
    );

    public function getCityJson()
    {
        Varien_Profiler::start('TEST: '.__METHOD__);
        if (!$this->_regionJson) {
            $cacheKey = 'DIRECTORY_CITIES_JSON_STORE'.Mage::app()->getStore()->getId();
            if (Mage::app()->useCache('config')) {
                $json = Mage::app()->loadCache($cacheKey);
            }
            if (empty($json)) {
                $countryIds = array();
                foreach ($this->getCountryCollection() as $country) {
                    $countryIds[] = $country->getCountryId();
                }
                /* @var $collection Gri_Directory_Model_Resource_City_Collection */
                $collection = Mage::getModel('gri_directory/city')->getResourceCollection();
                $collection->addCountryFilter($countryIds)->load();
                /* @var $city Gri_Directory_Model_City */
                $cities = array();
                foreach ($collection as $city) {
                    if (!$city->getCityId()) {
                        continue;
                    }
                    $cities[$city->getCountryId()][$city->getRegionId()][$city->getCityId()] = array(
                        'code' => $city->getCode(),
                        'name' => $this->__($city->getName())
                    );
                }
                $json = Mage::helper('core')->jsonEncode($cities);

                if (Mage::app()->useCache('config')) {
                    Mage::app()->saveCache($json, $cacheKey, array('config'));
                }
            }
            $this->_regionJson = $json;
        }

        Varien_Profiler::stop('TEST: '.__METHOD__);
        return $this->_regionJson;
    }

    public function getCityLabels($asJson = FALSE)
    {
        $result = array();
        foreach ($this->_cityLabels as $k => $v) {
            $result[$k] = $this->__($v);
        }
        return $asJson ? Mage::helper('core')->jsonEncode($result) : $result;
    }

    /**
     * Return ISO2 country codes, which have optional city pre-configured
     *
     * @param bool $asJson
     * @return array|string
     */
    public function getCountriesWithOptionalCity($asJson = FALSE)
    {
        if (NULL === $this->_optionalCityCountries) {
            $this->_optionalCityCountries = preg_split('/\,/',
                Mage::getStoreConfig(self::CONFIG_PATH_OPTIONAL_CITY_COUNTRIES), 0, PREG_SPLIT_NO_EMPTY);
        }
        if ($asJson) {
            return Mage::helper('core')->jsonEncode($this->_optionalCityCountries);
        }
        return $this->_optionalCityCountries;
    }

    /**
     * @param Mage_Directory_Model_Currency $currency
     * @return string
     */
    public function getCurrencySymbol($currency)
    {
        if ($currency->getSymbol() === NULL) {
            $formatted = $currency->formatTxt(0);
            $number = $currency->formatTxt(0, array('display' => Zend_Currency::NO_SYMBOL));
            $currency->setSymbol(str_replace($number, '', $formatted));
        }
        return $currency->getSymbol();
    }

    public function getRegionLabels($asJson = FALSE)
    {
        $result = array();
        foreach ($this->_regionLabels as $k => $v) {
            $result[$k] = $this->__($v);
        }
        return $asJson ? Mage::helper('core')->jsonEncode($result) : $result;
    }

    /**
     * @return array
     */
    public function getInternalCode()
    {
        return $this->_internalCodes;
    }
}
