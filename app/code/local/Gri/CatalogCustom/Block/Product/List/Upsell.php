<?php
class Gri_CatalogCustom_Block_Product_List_Upsell extends Mage_Catalog_Block_Product_List_Upsell
{

    protected function _prepareData()
    {
        $product = $this->getProduct();
        $limit = $this->getItemLimit('upsell');


        /* @var $product Gri_CatalogCustom_Model_Product */
        $this->_itemCollection = $product->getCurrentCategoryProductCollection($limit)
            // ->joinAttribute('name', 'catalog_product/name', 'entity_id')
            // ->joinAttribute('brand', 'catalog_product/brand', 'entity_id')
            // ->joinAttribute('image', 'catalog_product/image', 'entity_id', NULL, 'left')
            ->addPriceData()
            ->addStoreFilter();
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($this->_itemCollection);
//      Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($this->_itemCollection);

        if ($limit > 0) {
            $this->_itemCollection->setPageSize($limit);
        }

        $this->_itemCollection->load();

        /**
         * Updating collection with desired items
         */
        Mage::dispatchEvent('catalog_product_upsell', array(
            'product' => $product,
            'collection' => $this->_itemCollection,
            'limit' => $this->getItemLimit()
        ));

        foreach ($this->_itemCollection as $product) {
            $product->setDoNotUseCategoryId(TRUE);
        }

        return $this;
    }

    public function getItems()
    {
        if ($this->_items === NULL) {
            $this->_items = $this->getItemCollection()->getItems();
            shuffle($this->_items);
        }
        return $this->_items;
    }
}
