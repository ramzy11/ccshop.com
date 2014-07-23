<?php

class Gri_FlashSale_Model_Resource_FlashSale_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{

    public function _construct()
    {
        $this->_init('gri_flashsale/flashSale');
    }
}
