<?php

class Gri_ColorFilter_Model_Resource_ColorFilter extends Mage_Core_Model_Resource_Db_Abstract
{

    protected function _construct()
    {
        $this->_init('gri_colorfilter/colorfilter', 'color_id');
    }

    public function getActiveColorId()
    {
        return $this->getReadConnection()
            ->fetchOne("SELECT `{$this->getIdFieldName()}`
                        FROM `{$this->getMainTable()}`
                        WHERE `is_active` = 1" );
    }
}
