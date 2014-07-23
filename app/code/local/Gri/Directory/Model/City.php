<?php

/**
 * @method Gri_Directory_Model_Resource_City _getResource()
 * @method Gri_Directory_Model_Resource_City getResource()
 */
class Gri_Directory_Model_City extends Mage_Core_Model_Abstract
{

    protected function _construct()
    {
        $this->_init('gri_directory/city');
    }

    public function getName()
    {
        $name = $this->getData('name');
        if (is_null($name)) {
            $name = $this->getData('default_name');
        }
        return $name;
    }

    public function loadByCode($code, $countryId)
    {
        if ($code) {
            $this->_getResource()->loadByCode($this, $code, $countryId);
        }
        return $this;
    }

    public function loadByName($name, $countryId)
    {
        $this->_getResource()->loadByName($this, $name, $countryId);
        return $this;
    }
}
