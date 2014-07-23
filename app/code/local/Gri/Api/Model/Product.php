<?php

class Gri_Api_Model_Product extends Mage_Core_Model_Abstract {

    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('gri_api/product');
    }

}