<?php

class Gri_Affiliate_Model_Resource_Order_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{

    public function _construct()
    {
        $this->_init('gri_affiliate/order');
    }
}
