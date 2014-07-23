<?php
class Gri_CatalogCustom_Block_Product_List extends Mage_Catalog_Block_Product_List
{

    CONST CONFIG_PATH_CATALOG_GRID_COLUMN = 'catalog/frontend/grid_column';
    public $mode = 'general';

    /**
     * @return Gri_CatalogCustom_Model_Category
     */
    public function getCurrentCategory()
    {
        if (!$this->hasData('current_category')) {
            $this->setData('current_category', Mage::registry('current_category'));
        }
        return $this->getData('current_category');
    }

    public function getEditorsPickProductCollection()
    {

        return $this->getCurrentCategory()->getEditorsPickCollection();
    }

    public function getBestSellerCollection()
    {
        return $this->getCurrentCategory()->getBestSellerCollection();
    }

    public function getLoadedProductCollection()
    {
        $productCollection = $this->_getProductCollection();
        if (in_array($this->getRequest()->getRouteName(), array('catalogsearch', 'gri_catalogsearch')) && $this->getRequest()->getControllerName() == 'result') {
            if ($this->getRequest()->getParam('brand')) {
                $productCollection->addFieldToFilter('brand', $this->getRequest()->getParam('brand'));
            }
            // define search priority sort in search result
            $productCollection->addAttributeToSort('priority', 'desc');
            $productCollection->addAttributeToSort('created_at', 'desc');
        }
        return $productCollection;
    }

    public function getColumnCount()
    {
        $admin_column = Mage::getStoreConfig(self::CONFIG_PATH_CATALOG_GRID_COLUMN);
        if ($admin_column) return (int)$admin_column;
        if (!$this->_getData('column_count')) {
            $pageLayout = $this->getPageLayout();
            if ($pageLayout && $this->getColumnCountLayoutDepend($pageLayout->getCode())) {
                $this->setData(
                    'column_count',
                    $this->getColumnCountLayoutDepend($pageLayout->getCode())
                );
            } else {
                $this->setData('column_count', $this->_defaultColumnCount);
            }
        }

        return (int) $this->_getData('column_count');
    }
}
