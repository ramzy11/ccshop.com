<?php
class Gri_CatalogCustom_Block_Product_List_Toolbar extends Mage_Catalog_Block_Product_List_Toolbar
{
    protected $_direction = 'desc';
    protected $_orderField = 'created_at';

    public function getAvailableOrders()
    {
        /*$orders = array (
            'created_at' => 'Newest First',
            'position' => 'Position',
            'best_seller' => 'Best Sellers',
            'price' => array(
                array('label' => 'Price - High to Low', 'dir' => 'desc'),
                array('label' => 'Price - Low to High', 'dir' => 'asc')
            ),
        );*/

//        $orders = array (
//            'created_at' => 'New In',
//            'position' => 'Position',
//            'best_seller' => 'Best Sellers',
//            'price' => array(
//                array('label' => 'Price - High to Low', 'dir' => 'desc'),
//                array('label' => 'Price - Low to High', 'dir' => 'asc')
//            ),
//        );

        /* @var $_catalogCustomHelper Gri_CatalogCustom_Helper_Data */
        $_catalogCustomHelper = Mage::helper('gri_catalogcustom');
        $orders = $_catalogCustomHelper->getAvailableOrders(1);

        /* @var $category Mage_Catalog_Model_Category */
        if (($category = Mage::registry('current_category')) && $category->getDynamicAttributes()) unset($orders['position']);
        return $orders;
    }



    public function getLimit()
    {
        $limit = $this->getRequest()->getParam($this->getLimitVarName());
        $limit == 'all' or $limit = $this->getDefaultPerPageValue();
        return $limit;
    }

    public function isOrderNow($order, $dir)
    {
        $this->_orderField = 'created_at';
        $currentDir = $this->getCurrentDirection();
        return ($order == $this->getCurrentOrder() && $dir == $currentDir);
    }


    /**
     * Get grit products sort order field
     *
     * @return string
     */
    public function getCurrentOrder()
    {
        $order = $this->_getData('_current_grid_order');
        if ($order) {
            return $order;
        }

        $orders = $this->getAvailableOrders();
        $defaultOrder = $this->_orderField;

        if (!isset($orders[$defaultOrder])) {
            $keys = array_keys($orders);
            $defaultOrder = $keys[0];
        }

        $order = $this->getRequest()->getParam($this->getOrderVarName());
        if ($order && isset($orders[$order])) {
            if ($order == $defaultOrder) {
                Mage::getSingleton('catalog/session')->unsSortOrder();
            } else {
                $this->_memorizeParam('sort_order', $order);
            }
        } else {
            $order = Mage::getSingleton('catalog/session')->getSortOrder();
        }
        // validate session value
        $isAjax = $this->getRequest()->getParam('isAjax');  //  clear sort cache
        if (!$isAjax || !$order || !isset($orders[$order])) {
            $order = $defaultOrder;
        }

        $this->setData('_current_grid_order', $order);

        return $order;
    }

    public function getPagerHtml()
    {
        if (!$this->getChild('product_list_toolbar_pager')) {
            $this->setChild('product_list_toolbar_pager', $this->getLayout()->createBlock('page/html_pager'));
        }
        return parent::getPagerHtml();
    }

    /**
     * @param Mage_Catalog_Model_Resource_Product_Collection $collection
     * @return Gri_CatalogCustom_Block_Product_List_Toolbar
     */
    public function setCollection($collection)
    {
        $this->_collection = $collection;

        $this->_collection->setCurPage($this->getCurrentPage());

        // we need to set pagination only if passed value integer and more that 0
        $limit = (int)$this->getLimit();
        if ($limit) {
            $this->_collection->setPageSize($limit);
        }
        if ($order = $this->getCurrentOrder()) {
            if ($order == 'best_seller') {
                $collection->addAttributeToSort('best_seller', $collection::SORT_ORDER_DESC)
                    ->addAttributeToSort('qty_ordered', $collection::SORT_ORDER_DESC)
                    ->addAttributeToSort('entity_id', $collection::SORT_ORDER_DESC);
            } else {
                $this->_collection->setOrder($order, $this->getCurrentDirection());
            }
        }
        return $this;
    }

    /**
     *  get  sort by title
     *
     */
    public function  geSortByTitle()
    {
        $orders = $this->getAvailableOrders();
        $order = $this->getCurrentOrder();
        $dir = $this->getCurrentDirection();
        $title = '';
        switch (strtolower($order)) {
            case 'best_seller':
            case 'created_at':
                $title = $this->__($orders[$order]);
                break;
            case 'price':
                foreach ($orders['price'] as $price) {
                    if ($price['dir'] == strtolower($dir)) {
                        $title = $this->__($price['label']);
                    }
                }
                break;
            default:
                $title = $this->__('Sort by');
                break;
        }
        return $title;
    }

    /**
     * Retrieve default per page values
     *
     * @return string (comma separated)
     */
    public function getDefaultPerPageValue()
    {
        if ($this->getCurrentMode() == 'list') {
            if ($default = $this->getDefaultListPerPage()) {
                return $default;
            }
            return Mage::getStoreConfig('catalog/frontend/list_per_page');
        }
        elseif ($this->getCurrentMode() == 'grid') {
            if($this->getAction()->getFullActionName() == 'catalogsearch_advanced_result'){
                return Mage::getStoreConfig('catalog/frontend/adsearch_grid_per_page');
            }
            elseif($this->getAction()->getFullActionName() == 'gri_flashsale_index_index'){
                return Mage::getStoreConfig('flashsale/settings/flashsale_per_page');
            }
            elseif ($default = $this->getDefaultGridPerPage()) {
                return $default;
            }
            return Mage::getStoreConfig('catalog/frontend/grid_per_page');
        }
        return 0;
    }
}
