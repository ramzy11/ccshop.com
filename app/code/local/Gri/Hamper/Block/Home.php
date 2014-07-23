<?php

class Gri_Hamper_Block_Home extends Mage_Catalog_Block_Product_Abstract
{

    /**
     * @param Mage_Catalog_Model_Product $product
     * @return int
     */
    public function getChildrenProductCount($product)
    {
        if ($product->getData('children_count') === NULL) {
            /* @var $type Gri_Hamper_Model_Product_Type */
            $type = $product->getTypeInstance();
            $children = $type->getChildrenIds($product->getId(), FALSE);
            $count = 0;
            foreach ($children as $ids) {
                $count += count($ids);
            }
            $product->setData('children_count', $count);
        }
        return $product->getData('children_count');
    }

    /**
     * @return Mage_Catalog_Model_Product
     */
    public function getCustomHamper()
    {
        if ($this->_getData('custom_hamper') === NULL) {
            /* @var $collection Mage_Catalog_Model_Resource_Product_Collection */
            $collection = Mage::getModel('catalog/product')->getCollection();
            $collection->setStore(Mage::app()->getStore())
                ->addPriceData()
                ->addAttributeToFilter('type_id', Gri_Hamper_Model_Product_Type::TYPE_HAMPER)
                ->addAttributeToFilter('price_type', Gri_Hamper_Model_Product_Price::PRICE_TYPE_DYNAMIC)
                ->setPage(1, 1);
            /* @var $product Mage_Catalog_Model_Product */
            $product = $collection->getFirstItem();
            $this->setData('custom_hamper', $product->load(NULL));
        }
        return $this->_getData('custom_hamper');
    }

    /**
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function getPresetHampers()
    {
        if ($this->_getData('preset_hampers') === NULL) {
            /* @var $collection Mage_Catalog_Model_Resource_Product_Collection */
            $collection = Mage::getModel('catalog/product')->getCollection();
            $collection->addAttributeToSelect(array('small_image', 'name', 'hamper_pick_limit'))
                ->setStore(Mage::app()->getStore())
                ->addPriceData()
                ->addAttributeToFilter('type_id', Gri_Hamper_Model_Product_Type::TYPE_HAMPER)
                ->addAttributeToFilter('price_type', Gri_Hamper_Model_Product_Price::PRICE_TYPE_FIXED);
            $this->setData('preset_hampers', $collection);
        }
        return $this->_getData('preset_hampers');
    }
}
