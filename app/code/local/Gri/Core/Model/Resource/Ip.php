<?php

class Gri_Core_Model_Resource_Ip extends Mage_Core_Model_Resource_Db_Abstract
{

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('gri_core/ip_to_country', 'from');
    }
}
