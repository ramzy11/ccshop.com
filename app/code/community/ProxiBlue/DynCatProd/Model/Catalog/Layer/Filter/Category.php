<?php

class ProxiBlue_DynCatProd_Model_Catalog_Layer_Filter_Category extends Mage_Catalog_Model_Layer_Filter_Category {

    /**
     * Get data array for building category filter items
     *
     * @return array
     */
    protected function _getItemsData() {
        $data = array();
        $collection = clone $this->getLayer()->getProductCollection();
        if ($collection->hasFlag('is_dynamic')) {
            try {
                $filters = $this->getLayer()->getState()->getFilters();
                $hasCategoryFilter = false;
                array_walk($filters, function ($filter) use (&$hasCategoryFilter) {
                            if ($filter->getFilter() instanceof Mage_Catalog_Model_Layer_Filter_Category) {
                                $hasCategoryFilter = true;
                                return;
                            }
                        });
                if ($hasCategoryFilter == False) {
                    // reset columns, order and limitation conditions
                    $collection->getSelect()->reset(Zend_Db_Select::ORDER);
                    $collection->getSelect()->reset(Zend_Db_Select::LIMIT_COUNT);
                    $collection->getSelect()->reset(Zend_Db_Select::LIMIT_OFFSET);
                    $collection->clear();
                    $collection->setPageSize(false);
                    $categories = array();
                    $items = $collection->getItems();
                    array_walk($items, function ($product) use (&$categories) {
                                $categoryArray = $product->getCategoryIds();
                                foreach ($categoryArray as $cat) {
                                    $categories[] = $cat;
                                }
                            });
                    // add any child categories this category may have
                    $childCategories = $this->getCategory()->getChildrenCategories();
                    foreach ($childCategories as $cat) {
                        if ($cat instanceof Mage_Catalog_Model_Category) {
                            $categories[] = $cat->getId();
                        }
                    }
                    $counted = array_count_values($categories);
                    $categories = array_unique($categories);

                    $categoryCollection = Mage::getModel('catalog/category')->getCollection()
                            ->addAttributeToSelect('url_key')
                            ->addAttributeToSelect('name')
                            ->addAttributeToSelect('is_anchor')
                            ->addAttributeToFilter('is_active', 1)
                            ->addAttributeToFilter('include_in_menu', 1)
                            ->addIdFilter($categories)
                            ->addAttributeToSort('name')
                            ->load();
                    foreach ($categoryCollection as $category) {
                        $data[] = array(
                            'label' => Mage::helper('core')->htmlEscape($category->getName()),
                            'value' => $category->getId(),
                            'count' => (array_key_exists($category->getId(), $counted)) ? $counted[$category->getId()] : '',
                        );
                    }
                }
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }
        if (count($data) == 0) {
            $data = parent::_getItemsData();
        }
        return $data;
    }

    /**
     * Get filter value for reset current filter state
     *
     * @return mixed
     */
    public function getResetValue() {
        $collection = $this->getLayer()->getProductCollection();
        if ($collection->hasFlag('is_dynamic')) {
            return null;
        }
        return parent::getResetValue();
    }

}
