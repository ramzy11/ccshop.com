<?php

/**
 * Additional fields
 */
class D1m_Customer_Block_Form_Additional extends Mage_Core_Block_Template {
    /**
     * Get Current Allowed Countries
     * @return array
     */
    public function GetAllowedCountries() {
        /** @var Mage_Directory_Model_Country $countryModel */
        $countryModel = Mage::getModel('directory/country');
        return $countryModel->getResourceCollection()->loadByStore()->toOptionArray(true);
    }

    public function getFieldIdFormat()
    {
        if (!$this->hasData('field_id_format')) {
            $this->setData('field_id_format', '%s');
        }
        return $this->getData('field_id_format');
    }

    public function getFieldNameFormat()
    {
        if (!$this->hasData('field_name_format')) {
            $this->setData('field_name_format', '%s');
        }
        return $this->getData('field_name_format');
    }

    public function getFieldId($field)
    {
        return sprintf($this->getFieldIdFormat(), $field);
    }

    public function getFieldName($field)
    {
        return sprintf($this->getFieldNameFormat(), $field);
    }
}