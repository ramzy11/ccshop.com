<?php

Class Gri_ShopByLook_Block_Product_View_Navigate extends Mage_Catalog_Block_Product_Abstract {

    public function getUponProduct($direction) {

        $visibility = array(
            Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
            Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG
        );
        $_product = Mage::getResourceModel('reports/product_collection')
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('attribute_set_id', 14)
                ->addAttributeToFilter('visibility', $visibility)
                ->setPageSize(1)
        ;
        Mage::registry('current_category') and $_product->addCategoryFilter(Mage::registry('current_category'));
        if ($direction == 'previous') {
            $_product->setOrder('e.entity_id', 'desc');
            $_product->getSelect()->where('e.entity_id<?', $this->getProduct()->getId());
        } elseif ($direction == 'next') {
            $_product->setOrder('e.entity_id', 'asc');
            $_product->getSelect()->where('e.entity_id>?', $this->getProduct()->getId());
        }
        /* @var $_product Mage_Catalog_Model_Product */
        $_product = $_product->getFirstItem();
        Mage::registry('current_category') and $_product->setCategoryId(Mage::registry('current_category')->getId());

        return $_product;
    }

}