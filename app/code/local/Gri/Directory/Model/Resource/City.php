<?php

class Gri_Directory_Model_Resource_City extends Mage_Core_Model_Resource_Db_Abstract
{
    protected $_cityNameTable;

    protected function _construct()
    {
        $this->_init('gri_directory/region_city', 'city_id');
        $this->_cityNameTable = $this->getTable('gri_directory/city_name');
    }

    protected function _getLoadSelect($field, $value, $object)
    {
        $select  = parent::_getLoadSelect($field, $value, $object);
        $adapter = $this->_getReadAdapter();

        $locale       = Mage::app()->getLocale()->getLocaleCode();
        $systemLocale = Mage::app()->getDistroLocaleCode();

        $cityField = $adapter->quoteIdentifier($this->getMainTable() . '.' . $this->getIdFieldName());

        $condition = $adapter->quoteInto('lcn.locale = ?', $locale);
        $select->joinLeft(
            array('lcn' => $this->_cityNameTable),
            "{$cityField} = lcn.city_id AND {$condition}",
            array());

        if ($locale != $systemLocale) {
            $nameExpr  = $adapter->getCheckSql('lcn.city_id is null', 'scn.name', 'lcn.name');
            $condition = $adapter->quoteInto('scn.locale = ?', $systemLocale);
            $select->joinLeft(
                array('scn' => $this->_cityNameTable),
                "{$cityField} = scn.city_id AND {$condition}",
                array('name' => $nameExpr));
        } else {
            $select->columns(array('name'), 'lcn');
        }

        return $select;
    }

    protected function _loadByCountry($object, $countryId, $value, $field)
    {
        $adapter = $this->_getReadAdapter();
        $locale = Mage::app()->getLocale()->getLocaleCode();
        $joinCondition = $adapter->quoteInto('cn.city_id = city.city_id AND cn.locale = ?', $locale);
        $select = $adapter->select()
            ->from(array('city' => $this->getMainTable()))
            ->joinLeft(
                array('cn' => $this->_cityNameTable),
                $joinCondition,
                array('name'))
            ->where('city.country_id = ?', $countryId)
            ->where("city.{$field} = ?", $value);

        $data = $adapter->fetchRow($select);
        if ($data) {
            $object->setData($data);
        }

        $this->_afterLoad($object);

        return $this;
    }

    public function loadByCode(Gri_Directory_Model_City $city, $cityCode, $countryId)
    {
        return $this->_loadByCountry($city, $countryId, (string)$cityCode, 'code');
    }

    public function loadByName(Mage_Directory_Model_Region $city, $cityName, $countryId)
    {
        return $this->_loadByCountry($city, $countryId, (string)$cityName, 'default_name');
    }
}
