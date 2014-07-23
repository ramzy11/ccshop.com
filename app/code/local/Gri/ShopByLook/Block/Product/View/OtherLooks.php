<?php

class Gri_ShopByLook_Block_Product_View_OtherLooks extends Mage_Catalog_Block_Product_View
{

    /**
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function getOtherLooks()
    {
        $currentCategory = Mage::registry('current_category');
        if (!$currentCategory) {
            $categoryIds = Mage::registry('product')->getCategoryIds();
            $currentCategory = Mage::getModel('catalog/category')->load(end($categoryIds));
        }
        /* @var $product Mage_Catalog_Model_Product */
        $product = Mage::registry('product');
        $collection = $product->getCollection();
        $collection->addAttributeToSelect('*')
            ->setStoreId($this->getStoreId())
            ->addCategoryFilter($currentCategory)
            ->addAttributeToFilter('entity_id', array("neq" => $product->getId()));
        $collection->setEntity($product->getResource());
        return $collection;
    }
}
