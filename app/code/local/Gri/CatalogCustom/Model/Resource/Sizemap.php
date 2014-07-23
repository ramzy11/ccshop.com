<?php

class Gri_CatalogCustom_Model_Resource_Sizemap extends Mage_Core_Model_Resource_Db_Abstract
{

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('gri_catalogcustom/sizemap', 'mapping_id');
    }
}
