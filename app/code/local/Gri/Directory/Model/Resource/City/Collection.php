<?php

class Gri_Directory_Model_Resource_City_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected $_cityNameTable;
    protected $_countryTable;

    protected function _construct()
    {
        $this->_init('gri_directory/city');

        $this->_countryTable    = $this->getTable('directory/country');
        $this->_cityNameTable = $this->getTable('gri_directory/city_name');

        $this->addOrder('name', Varien_Data_Collection::SORT_ORDER_ASC);
        $this->addOrder('default_name', Varien_Data_Collection::SORT_ORDER_ASC);
    }

    protected function _initSelect()
    {
        parent::_initSelect();
        $locale = Mage::app()->getLocale()->getLocaleCode();

        $this->addBindParam(':city_locale', $locale);
        $this->getSelect()->joinLeft(
            array('cn' => $this->_cityNameTable),
            'main_table.city_id = cn.city_id AND cn.locale = :city_locale',
            array('name'))
            ->where('main_table.is_active = 1');

        return $this;
    }

    /**
     * Filter by City code
     *
     * @param string|array $cityCode
     * @return Gri_Directory_Model_Resource_City_Collection
     */
    public function addCityCodeFilter($cityCode)
    {
        if (!empty($cityCode)) {
            if (is_array($cityCode)) {
                $this->addFieldToFilter('main_table.code', array('in' => $cityCode));
            } else {
                $this->addFieldToFilter('main_table.code', $cityCode);
            }
        }
        return $this;
    }

    /**
     * Filter by city name
     *
     * @param string|array $cityName
     * @return Gri_Directory_Model_Resource_City_Collection
     */
    public function addCityNameFilter($cityName)
    {
        if (!empty($cityName)) {
            if (is_array($cityName)) {
                $this->addFieldToFilter('main_table.default_name', array('in' => $cityName));
            } else {
                $this->addFieldToFilter('main_table.default_name', $cityName);
            }
        }
        return $this;
    }

    /**
     * Filter by country code (ISO 3)
     *
     * @param string $countryCode
     * @return Gri_Directory_Model_Resource_City_Collection
     */
    public function addCountryCodeFilter($countryCode)
    {
        $this->getSelect()
            ->joinLeft(
                array('country' => $this->_countryTable),
                'main_table.country_id = country.country_id'
            )
            ->where('country.iso3_code = ?', $countryCode);

        return $this;
    }

    /**
     * Filter by country_id
     *
     * @param string|array $countryId
     * @return Gri_Directory_Model_Resource_City_Collection
     */
    public function addCountryFilter($countryId)
    {
        if ($countryId) {
            if (is_array($countryId)) {
                $this->addFieldToFilter('main_table.country_id', array('in' => $countryId));
            } else {
                $this->addFieldToFilter('main_table.country_id', $countryId);
            }
        }
        return $this;
    }

    /**
     * Convert collection items to select options array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = $this->_toOptionArray('city_id', 'default_name', array('title' => 'default_name'));
        if (count($options) > 0) {
            array_unshift($options, array(
                'title' => NULL,
                'value' => '0',
                'label' => Mage::helper('directory')->__('-- Please select --')
            ));
        }
        return $options;
    }
}
