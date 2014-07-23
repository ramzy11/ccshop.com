<?php

class Gri_CatalogInventory_Model_Stock_Item extends Mage_CatalogInventory_Model_Stock_Item
{

    public function checkQty($qty)
    {
        if (Mage::registry('force_inventory_subtract')) return TRUE;
        return parent::checkQty($qty);
    }

    public function getQty()
    {
        /* @var $flashSaleHelper Gri_FlashSale_Helper_Data */
        $flashSaleHelper = Mage::helper('gri_flashsale');
        return $flashSaleHelper->getSalableQty($this);
    }
}
