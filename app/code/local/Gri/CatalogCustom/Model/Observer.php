<?php
class Gri_CatalogCustom_Model_Observer extends Mage_Catalog_Model_Observer
{

    /**
     * @return Mage_Customer_Model_Session
     */
    public function getCustomerSession()
    {
        return Mage::getSingleton('customer/session');
    }

    public function addCatalogToTopmenuItems(Varien_Event_Observer $observer)
    {
        $menu = $observer->getMenu();
        $tree = $menu->getTree();
        //add home
//        $homeData = array(
//            'name' => Mage::helper('catalog')->__('Home'),
//            'id' => 'home',
//            'url' => Mage::getBaseUrl());
//
//        $homeNode = new Varien_Data_Tree_Node($homeData, 'id', $tree);
//        $menu->addChild($homeNode);
        // add New arrival
        // add left  menu
        Mage::helper('gri_catalogcustom')->addNodesLeftMenu($observer->getMenu());

        //Mage::helper('gri_catalogcustom')->addBrandToMenu($observer->getMenu());
        // default
        parent::addCatalogToTopmenuItems($observer);
        // add as seen in and events
        //Mage::helper('gri_catalogcustom')->addNodesAfterMenu($observer->getMenu());

        // add right menu
        Mage::helper('gri_catalogcustom')->addNodesRightMenu($observer->getMenu());

        //Mage::helper('gri_sales')->addSalesToMenu($observer->getMenu());
    }

    public function topNavHideEmptyCategoriesFlat(Varien_Event_Observer $observer)
    {
        /* @var $select Varien_Db_Select */
        $select = $observer->getEvent()->getSelect();
        $conn = $select->getAdapter();
        /* @var $resource Mage_Catalog_Model_Resource_Category */
        $resource = Mage::getResourceModel('catalog/category');
        if (Mage::getStoreConfig(Gri_CatalogCustom_Helper_Category::XML_PATH_NAVIGATION_HIDE_EMPTY)) {
            $select->distinct();
            // Join category-product table
            $visibility = array(
                Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
                Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG
            );
            $select->join(array('count_table' => $resource->getTable('catalog/category_product_index')),
                'count_table.store_id = main_table.store_id AND count_table.category_id = main_table.entity_id',
                array()
            )->where('count_table.visibility IN (?)', $visibility);
            // Join price table
            $select->join(array('price_index' => $resource->getTable('catalog/product_index_price')),
                implode(' AND ', array(
                    'price_index.entity_id = count_table.product_id',
                    $conn->quoteInto('price_index.website_id = ?', Mage::app()->getWebsite()->getId()),
                    $conn->quoteInto('price_index.customer_group_id = ?', $this->getCustomerSession()->getCustomerGroupId())
                )),
                array()
            );
            $select->group('main_table.entity_id');
        }
    }

    /**
     * @see dispatchEvent 'catalog_category_prepare_save'
     * @param Varien_Event_Observer $observer
     */
    public function unlimitedCategoryProducts(Varien_Event_Observer $observer)
    {
        /* @var $category Gri_CatalogCustom_Model_Category */
        $category = $observer->getEvent()->getCategory();
        /* @var $request Mage_Core_Controller_Request_Http */
        $request = $observer->getEvent()->getRequest();
        $data = $request->getPost();
        if (isset($data['category_products']) && !$category->getProductsReadonly()) {
            $products = array();
            $tmp = explode('&', $data['category_products']);
            foreach ($tmp as $value) {
                if (!$pos = strpos($value, '=')) continue;
                $products[substr($value, 0, $pos)] = substr($value, $pos + 1);
            }
            $category->setPostedProducts($products);
        }
    }
}
