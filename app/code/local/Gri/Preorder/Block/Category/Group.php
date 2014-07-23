<?php

class Gri_Preorder_Block_Category_Group extends Mage_Core_Block_Template {

    protected static $_jsAdded;

    protected function _construct() {
        parent::_construct();
        $this->getTemplate() or $this->setTemplate('catalog/category/group.phtml');
        $this->getLimit() or $this->setLimit(10);
    }

    public static function addJs() {
        if (self::$_jsAdded)
            return '';
        self::$_jsAdded = TRUE;
        return '<script type="text/javascript" src="' . Mage::getBaseUrl('js') . 'jquery/jquery.iosslider.min.js"></script>';
    }

    /**
     * @return Mage_Catalog_Model_Resource_Category_Collection|Mage_Catalog_Model_Resource_Category_Flat_Collection
     */
    public function getCategories() {
        if ($this->getData('categories') === NULL) {
            $ids = $this->getCategoryIds();
            $ids or $ids = 0;
            $ids = array_map('intval', explode(',', $ids));
            /* @var $categories Mage_Catalog_Model_Resource_Category_Collection|Mage_Catalog_Model_Resource_Category_Flat_Collection */
            $categories = Mage::getModel('catalog/category')->getCollection()
                    ->setStoreId(Mage::app()->getStore()->getId());
            $categories->addAttributeToSelect('name')->addAttributeToFilter('entity_id', array('in' => $ids));
            $ids = implode(',', $ids);
            $categories->getSelect()->order(new Zend_Db_Expr("FIND_IN_SET(`e`.`entity_id`, '{$ids}')"));
            $this->setData('categories', $categories);
        }
        return $this->getData('categories');
    }

    /**
     * Get category products collection
     *
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function getCategoryProducts(Mage_Catalog_Model_Category $category) {
        if ($category->getData('product_collection') === NULL) {
            $collection = $category->getProductCollection();
            $collection->addAttributeToSelect('*')
                    ->setPageSize($this->getLimit());
            Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
            Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($collection);
            $collection->addUrlRewrite($category->getId());
            $category->setData('product_collection', $collection);
        }
        return $category->getData('product_collection');
    }

    /**
     * @return Gri_CatalogCustom_Helper_Product
     */
    public function getProductHelper() {
        return $this->helper('gri_catalogcustom/product');
    }

    /**
     * Retrieve Search result list HTML output
     *
     * @return string
     */
    public function getProductListHtml() {
        return $this->getChildHtml('');
    }

    public function getBrandCategory() {
        return Mage::getSingleton('gri_catalogcustom/category')->getBrandCategory();
    }

    public function getCategoryHtml() {
        $bolck = Mage::getSingleton('cms/block');
        $block->load('');
    }

}