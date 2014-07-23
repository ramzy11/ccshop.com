<?php

class Gri_SimplePrice_Model_Observer extends Varien_Object
{

    /**
     * @return Gri_SimplePrice_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('gri_simpleprice');
    }

    public function updateSimplePrice(Varien_Event_Observer $observer)
    {
        /* @var $product Gri_CatalogCustom_Model_Product */
        $product = $observer->getEvent()->getProduct();
        $this->_getHelper()->updateSimplePrice($product);
    }
}
