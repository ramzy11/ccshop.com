<?php

/**
 * @method integer getLimit() Get Product Limit
 * @method integer getPage() Get Product Page
 */
class Gri_CatalogCustom_Block_Category_Group extends Mage_Core_Block_Template
{
    protected static $_jsAdded;

    protected function _construct()
    {
        parent::_construct();
        $this->getTemplate() or $this->setTemplate('catalog/category/group.phtml');
        $this->getLimit() or $this->setLimit(10);
        $this->getPage() or $this->setPage(1);
    }

    public static function addJs()
    {
        if (self::$_jsAdded) return '';
        self::$_jsAdded = TRUE;
        return '<script type="text/javascript" src="' . Mage::getBaseUrl('js') . 'jquery/jquery.iosslider.min.js"></script>';
    }

    /**
     * @return Mage_Catalog_Model_Resource_Category_Collection|Mage_Catalog_Model_Resource_Category_Flat_Collection
     */
    public function getCategories()
    {
        if ($this->getData('categories') === NULL) {
            $auto = FALSE;
            if ($ids = $this->getCategoryIds()) {
                $arrayIds = array_map('intval', explode(',', $ids));
                $ids = implode(',', $arrayIds);
            }
            else {
                $auto = TRUE;
                $ids = $this->getCurrentCategory()->getChildren();
                $arrayIds = explode(',', $ids);
            }
            /* @var $categories Mage_Catalog_Model_Resource_Category_Collection|Mage_Catalog_Model_Resource_Category_Flat_Collection */
            $categories = Mage::getModel('catalog/category')->getCollection()
                ->setStoreId(Mage::app()->getStore()->getId());
            $categories->addAttributeToSelect('name')->addAttributeToSelect('url_key')
                ->addAttributeToFilter('entity_id', array('in' => $arrayIds))
                ->addAttributeToFilter('is_active', 1);
            if ($auto) $categories->addAttributeToFilter('include_in_menu', 1)->setOrder('position', 'asc');
            else $categories->getSelect()->order(new Zend_Db_Expr("FIND_IN_SET(" .
                ($this->getFlatCategoryHelper()->isEnabled() ? '`main_table`' : '`e`') . ".`entity_id`, '{$ids}')"));
            $this->setData('categories', $categories);
        }
        return $this->getData('categories');
    }

    /**
     * Get category products collection
     *
     * @param Mage_Catalog_Model_Category $category
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function getCategoryProducts(Mage_Catalog_Model_Category $category)
    {
        if ($category->getData('product_collection') === NULL) {
            $collection = $category->getProductCollection();
            $collection->addPriceData()->addStoreFilter();
            $collection->addAttributeToSelect('*')
                ->setPage($this->getPage(), $this->getLimit());
            Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
            Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($collection);
            $collection->addUrlRewrite($category->getId());
            $collection->setOrder('created_at');
            $category->setData('product_collection', $collection);
        }
        return $category->getData('product_collection');
    }

    public function getColumnCount()
    {
        return $this->getData('column_count') ? $this->getData('column_count') : 3;
    }

    /**
     * @return Gri_CatalogCustom_Model_Category
     */
    public function getCurrentCategory()
    {
        if ($this->getData('current_category')) return $this->getData('current_category');
        return Mage::registry('current_category');
    }

    /**
     * @return Mage_Catalog_Helper_Category_Flat
     */
    public function getFlatCategoryHelper()
    {
        return $this->helper('catalog/category_flat');
    }

    public function getLoadGroupProductsUrl()
    {
        return $this->getUrl('catalogcustom/category/group', array(
            'id' => '{id}',
            'page' => '{page}',
            'size' => $this->getLimit(),
            'isAjax' => '1',
        ));
    }

    /**
     * @return Gri_CatalogCustom_Helper_Product
     */
    public function getProductHelper()
    {
        return $this->helper('gri_catalogcustom/product');
    }
}
