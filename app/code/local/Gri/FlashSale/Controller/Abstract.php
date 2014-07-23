<?php

class Gri_FlashSale_Controller_Abstract extends Mage_Core_Controller_Front_Action
{

    /**
     * @return Gri_FlashSale_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('gri_flashsale');
    }

    protected function _initFlashSale()
    {
        return $this->_getHelper()->getActiveFlashSale();
    }
}
