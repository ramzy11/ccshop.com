<?php

class Gri_Customer_Model_Customer extends Mage_Customer_Model_Customer
{
    const CONFIG_PATH_NAME_ELEMENTS = 'customer/address/name_elements';

    /**
     * @return Mage_Customer_Model_Group
     */
    public function getGroup()
    {
        if ($this->getData('group') === NULL) {
            $this->setData('group', Mage::getModel('customer/group')->load($this->getGroupId()));
        }
        return $this->getData('group');
    }

    public function getGroupCode()
    {
        if ($this->getData('group_code') === NULL) {
            $this->setData('group_code', $this->getGroup()->getCustomerGroupCode());
        }
        return $this->getData('group_code');
    }

    public function getName()
    {
        if ($elements = Mage::getStoreConfig(self::CONFIG_PATH_NAME_ELEMENTS)) {
            return strtr($elements, $this->getNameElements());
        }
        else return parent::getName();
    }

    public function getNameElements(array $attributes = NULL)
    {
        $result = array();
        $attributes === NULL and $attributes = array(
            'prefix', 'firstname', 'middlename', 'lastname', 'suffix'
        );
        foreach ($attributes as $k) {
            $result['{{' . $k . '}}'] = $this->getData($k);
        }
        return $result;
    }
}
