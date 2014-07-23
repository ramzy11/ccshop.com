<?php

class Gri_ColorFilter_Model_Resource_ColorFilter_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{

    public function _construct()
    {
        parent::_construct();
        $this->_init('gri_colorfilter/colorfilter');
    }
}
