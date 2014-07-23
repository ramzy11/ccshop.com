<?php

class Gri_CatalogCustom_Helper_Data extends Mage_Core_Helper_Abstract
{
    const CONFIG_PATH_NAVIGATION_MENU_LEFT = 'gri_navigation/nav/menu_left';
    const CONFIG_PATH_NAVIGATION_MENU_RIGHT = 'gri_navigation/nav/menu_right';
    const CONFIG_PATH_BRANDS_COLLECTIONS = 'gri_categories/brands_setting/brand_collections';
    const CONFIG_PATH_SHOP_COLLECTIONS = 'gri_categories/shop_setting/shop_collections';
    const CONFIG_PATH_DYN_COLLECTIONS = 'dyncatprod/vertnav/roots';


    /*public function getSizeValues($sizeinstance)
    {
        $data = $this->getAttributeOptionsWithAdminLabel($sizeinstance);
        $results = array();
        foreach ($data as $k => $v) {
            if (isset($v['universal_size'])) $k = $v['universal_size'];
            if (!isset($results[$k])) $results[$k] = '';
            $results[$k] .= $v['value'] . '_';
        }
        return $results;
    }

    public function getUniversalSize($adminSize)
    {
        if ($sizemap = Mage::getModel('gri_catalogcustom/sizemap')->loadByAttribute('admin_size', $adminSize)) return $sizemap->getData('universal_size');
        return false;

    }

    public function getAttributeOptionsWithAdminLabel($attribute, $attributeValueId = null)
    {
        $_collection = Mage::getResourceModel('eav/entity_attribute_option_collection')
            ->setStoreFilter(0)
            ->setAttributeFilter($attribute->getId())
            ->load();
        $data = array();
        foreach ($_collection->toOptionArray() as $_cur_option) {
            if ($attributeValueId != null && $_cur_option['value'] == $attributeValueId) return $_cur_option['label'];
            if (isset($data[$_cur_option['label']])) continue;
            $data[$_cur_option['label']] = array('value' => $_cur_option['value'], 'label' => $_cur_option['label']);
            if ($this->getUniversalSize($_cur_option['label'])) $data[$_cur_option['label']]['universal_size'] = $this->getUniversalSize($_cur_option['label']);
        }
        return $data;
    }*/

    /**
     * Convert nodes text to array
     */
    public function  convertTextToArray($txt)
    {
        $txt = explode("\n", $txt);

        $result = array();
        foreach ($txt as $k => $v) {
            if ($v = trim($v)) {
                $data = explode(',', $v);
                $sort = trim($data[0]);
                $label = isset($data[1]) ? trim($data[1]) : '';
                $uri = isset($data[2]) ? trim($data[2]) : '';
                $result[$k] = array(
                    'sort' => $sort,
                    'label' => $label,
                    //'uri' => $uri ? Mage::getUrl($uri) : 'javascript:;', // gri
                    'uri' => substr($uri,0,1) != '#' ? Mage::getUrl($uri) : $uri,// v2014
                );
            }
        }
        return $result;
    }

    /**
     *  Add nodes to the right of top navigation menu
     */
    public function  addNodesLeftMenu($topMenu)
    {
        $nodes = $this->convertTextToArray(Mage::getStoreConfig(self::CONFIG_PATH_NAVIGATION_MENU_LEFT));
        $this->addNodesToMenu($topMenu, $nodes, 'left');
    }

    /**
     *  Add nodes to the right of top navigation menu
     */
    public function  addNodesRightMenu($topMenu)
    {
        $nodes = $this->convertTextToArray(Mage::getStoreConfig(self::CONFIG_PATH_NAVIGATION_MENU_RIGHT));
        $this->addNodesToMenu($topMenu, $nodes, 'right');
    }

    /**
     *  Add nodes to top navigation menu
     */
    public function addNodesToMenu($topMenu, $nodes, $alias)
    {
        $tree = $topMenu->getTree();

        // $p: parent
        foreach ($nodes as $k => $p) {
            if ($p['sort'] == (int)$p['sort']) {
                $parentData = array(
                    'name' => $p['label'],
                    'id' => 'category-node-' . $alias . $k,
                    'url' => $p['uri'],
                );
                $parentNode = new Varien_Data_Tree_Node($parentData, 'id', $tree, $topMenu);
                // $c: child
                foreach ($nodes as $j => $c) {
                    if ($c['sort'] > $p['sort'] && $c['sort'] < $p['sort'] + 1) {
                        $categoryData = array(
                            'name' => $c['label'],
                            'id' => 'category-node-' . $alias . $j,
                            'url' => $c['uri'],
                        );
                        $childNode = new Varien_Data_Tree_Node($categoryData, 'id', $tree, $parentNode);
                        $parentNode->addChild($childNode);
                    }
                }
                $topMenu->addChild($parentNode);
            }
        }
    }

    /*public function addBrandToMenu($topMenu)
    {
        $tree = $topMenu->getTree();
        // addBrand
        $brandData = array(
            'name' => $this->__('Brands'),
            'id' => 'brand',
            'url' => 'javascript:void(0);',
        );
        $brandNode = new Varien_Data_Tree_Node($brandData, 'id', $tree, $topMenu);
        $brandItems = Mage::getModel('catalog/category')->getCollection()->addAttributeToSelect('*')
            ->addAttributeToFilter('url_key', array('in' => array('ninewest', 'stevemadden', 'betseyjohnson', 'eqiq')));
        foreach ($brandItems as $category) {
            $nodeId = 'category-node-' . $category->getId();
            $categoryData = array(
                'name' => $category->getName(),
                'id' => $nodeId,
                'url' => Mage::helper('catalog/category')->getCategoryUrl($category),
            );
            $categoryNode = new Varien_Data_Tree_Node($categoryData, 'id', $tree, $brandNode);
            $brandNode->addChild($categoryNode);
        }
        $topMenu->addChild($brandNode);
    }*/

    /*public function addNewArrivalToMenu($topMenu)
    {
        $tree = $topMenu->getTree();

        // add new arrival
        $topNewArrivalCategory = Mage::getModel('catalog/category')->getResourceCollection()
            ->addAttributeToFilter('url_key', 'new-arrivals')
            ->addAttributeToFilter('level', 2)->getFirstItem();
        $topNewArrival = array(
            'name' => $this->__('New Arrivals'),
            'id' => 'new-Arrival',
//          'url' => Mage::helper('catalog/category')->getCategoryUrl($topNewArrivalCategory),
            'url' => 'javascript:void(0);',
        );
        $newArrivalNode = new Varien_Data_Tree_Node($topNewArrival, 'id', $tree, $topMenu);

        // add shoes, clothing, accessories
        $newArrivalItems = Mage::getModel('catalog/category')->getCollection()->addAttributeToSelect('*')
            ->addAttributeToFilter('url_key', 'new-arrivals')
            ->addAttributeToFilter('level', array('eq' => 3));
        foreach ($newArrivalItems as $category) {
            $nodeId = 'category-node-' . $category->getId();
            $categoryData = array(
                'name' => $category->getParentCategory()->getName(),
                'id' => $nodeId,
                'url' => Mage::helper('catalog/category')->getCategoryUrl($category),
            );
            $categoryNode = new Varien_Data_Tree_Node($categoryData, 'id', $tree, $newArrivalNode);
            $newArrivalNode->addChild($categoryNode);
        }
        $topMenu->addChild($newArrivalNode);
    }*/

    /**
     *  @return Array
     */
    public function getLeftMenuArr(){
        $nodes = $this->convertTextToArray(Mage::getStoreConfig(self::CONFIG_PATH_NAVIGATION_MENU_LEFT));
        return $nodes;
    }

    /**
     * @param string $sku
     * @return Gri_CatalogCustom_Model_Product
     */
    public function getProductBySku($sku)
    {
        /* @var $product Mage_Catalog_Model_Product */
        $product = Mage::getSingleton('catalog/product');
        $productId = $product->unsetData()->getIdBySku($sku);
        return $productId ? $product->unsetData()->load($productId) : FALSE;
    }

    /**
     * @return array
     */
    public function getStoreBrands()
    {
        $brands = str_replace("\n\n", "\n", str_replace("\r", "\n", trim(Mage::getStoreConfig(self::CONFIG_PATH_BRANDS_COLLECTIONS))));
        return explode("\n", $brands);
    }

    /**
     * @return array
     */
    public function getStoreShop()
    {
        $shop = str_replace("\n\n", "\n", str_replace("\r", "\n", trim(Mage::getStoreConfig(self::CONFIG_PATH_SHOP_COLLECTIONS))));
        return explode("\n", $shop);
    }

    public function getDynCategoryUrlKeys()
    {
        $shop = str_replace("\n\n", "\n", str_replace("\r", "\n", trim(Mage::getStoreConfig(self::CONFIG_PATH_DYN_COLLECTIONS))));
        return explode("\n", $shop);
    }


    public function getAvailableOrders( $mod = 1)
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

        if($mod){
            $orders = array (
                'created_at' => 'New In',
                'position' => 'Position',
                'best_seller' => 'Best Sellers',
                'price' => array (
                    array('label' => 'Price - High to Low', 'dir' => 'desc'),
                    array('label' => 'Price - Low to High', 'dir' => 'asc')
                ),
            );
        }else{
            $orders = array (
                'created_at'=>  array('desc'=> 'New In'),
                'position'=> array('desc'=> 'Position'),
                'best_seller'=> array('desc'=> 'Best Sellers'),
                'price'=> array('desc'=> 'Price - High to Low', 'asc'=> 'Price - Low to High' ),
            );
        }

        return $orders;
    }
}
