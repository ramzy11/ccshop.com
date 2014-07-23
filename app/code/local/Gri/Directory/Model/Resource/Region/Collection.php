<?php

class Gri_Directory_Model_Resource_Region_Collection extends Mage_Directory_Model_Resource_Region_Collection
{

    protected function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()->where('main_table.is_active = 1');
        return $this;
    }
}
