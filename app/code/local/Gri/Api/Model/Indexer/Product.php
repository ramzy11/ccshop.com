<?php

class Gri_Api_Model_Indexer_Product extends Mage_Catalog_Model_Resource_Product_Indexer_Price_Default
{

    public function addAttributeToSelect($select, $attrCode, $entity, $store, $condition = null, $required = false)
    {
        return $this->_addAttributeToSelect($select, $attrCode, $entity, $store, $condition, $required);
    }
}