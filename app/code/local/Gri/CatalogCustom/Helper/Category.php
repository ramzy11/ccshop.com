<?php

class Gri_CatalogCustom_Helper_Category extends Mage_Core_Helper_Abstract
{
    const XML_PATH_NAVIGATION_HIDE_EMPTY = 'catalog/navigation/hide_empty';

    protected $_brandCategories;
    protected $_shopCategories;
    protected $_dynCategories;
    protected $_categories = array();
    protected $_localeFiles = array(
        'en_US' => 'Eng',
        'fr_FR' => 'Fra',
        'ja_JP' => 'Jap',
        'zh_CN' => 'Chs',
        'zh_HK' => 'Cht',
    );
    protected $_urlKeys;

    public function createCategories(array $definition, Mage_Catalog_Model_Category $root, array $blocks = array())
    {
        $stores = Mage::app()->getStores();
        $defaultData = array(
            'is_active' => 1,
            'include_in_menu' => 1,
            'display_mode' => Mage_Catalog_Model_Category::DM_MIXED,
            'is_anchor' => 1,
        );
        $globalAttributes = array(
            'is_active',
            'url_key',
            'include_in_menu',
            'display_mode',
            'is_anchor',
        );
        /* @var $urlResource Mage_Catalog_Model_Resource_Url */
        $urlResource = Mage::getResourceModel('catalog/url');
        foreach ($definition as $urlKey => $data) {
            $category = $this->getCategory($root, $urlKey);
            $category or $category = Mage::getModel('catalog/category')->setStoreId(0);
            $data = array_merge($defaultData, $data);
            $data['url_key'] = $urlKey;
            $data['path'] = $root->getPath();
            if($category->getId()){
                $data['path'] = $root->getPath().'/'.$category->getId();
            }
            isset($data['landing_page']) && isset($blocks[1][$data['landing_page']]) and
                $data['landing_page'] = $blocks[1][$data['landing_page']];
            $category->addData($data);

            if(!$category->getId()){
                $category->save();
            }

            $urlResource->saveCategoryAttribute($category, 'url_key');
            $cid = $category->getId();
            if (!empty($data['children'])) $this->createCategories($data['children'], $category, $blocks);
            foreach ($stores as $storeId => $store) {
                $category->unsetData()->setStoreId($storeId)->load($cid);
                foreach ($globalAttributes as $key) {
                    $category->setData($key, FALSE);
                }
                if (isset($data['landing_page']) && isset($blocks[$storeId][$data['landing_page']])) {
                    $category->setLandingPage($blocks[$storeId][$data['landing_page']]);
                }
                $category->save();
            }
        }
    }

    public function getContentByStore(Mage_Core_Model_Store $store)
    {
        $contentPath = dirname(__FILE__) . DS . 'Category' . DS;
        $localeCode = Mage::getStoreConfig('general/locale/code', $store);
        $contentPath .= isset($this->_localeFiles[$localeCode]) ?
            $this->_localeFiles[$localeCode] : reset($this->_localeFiles);
        $contentPath .= '.php';
        return is_file($contentPath) ? include($contentPath) : array();
    }

    public function updateStoreCategories(Mage_Core_Model_Store $store)
    {
        /* @var $category Gri_CatalogCustom_Model_Category */
        $category = Mage::getModel('catalog/category');
        $resource = $category->getResource();
        $select = $resource->getReadConnection()->select();
        $nameAttribute = $resource->getAttribute('name');
        $content = $this->getContentByStore($store);
        $storeId = $store->getId();
        $select->from(array('n' => $nameAttribute->getBackendTable()), array(
            'entity_type_id',
            'attribute_id',
            'entity_id',
            'value',
        ))->where('n.store_id = 0')
            ->where('n.attribute_id = ?', $nameAttribute->getAttributeId())
            ->where('n.value IN (?)', array_keys($content));
        $data = $select->query()->fetchAll();
        foreach ($data as &$v) {
            if (isset($content[$v['value']]['name']) && $v['value'] != $content[$v['value']]['name']) {
                $v['value'] = $content[$v['value']]['name'];
                $v['store_id'] = $storeId;
            } else {
                $v = NULL;
            }
        }
        unset($v);
        $data = array_filter($data);
        $write = $resource->getWriteConnection();
        $write->beginTransaction();
        $write->insertOnDuplicate($nameAttribute->getBackendTable(), $data);
        $categoryFlatTable = 'catalog_category_flat_store_' . $storeId;
        if ($write->isTableExists($categoryFlatTable)) foreach ($data as $v) {
            $write->update($categoryFlatTable, array('name' => $v['value']), array('entity_id = ?' => $v['entity_id']));
        }
        $write->commit();
    }

    /**
     * @param Mage_Core_Model_Store $store
     * @return Mage_Catalog_Model_Resource_Category_Collection
     */
    public function getBrandCategories($store = NULL)
    {
        if (!$this->_brandCategories) {
            $brands = Mage::helper('gri_catalogcustom')->getStoreBrands();
            $this->_brandCategories = $this->getCategoriesByUrlKeys($brands, $store);
        }
        return $this->_brandCategories;
    }

    /**
     * @param Mage_Core_Model_Store $store
     * @return Mage_Catalog_Model_Resource_Category_Collection
     */
    public function getShopCategories($store ,$level = 2)
    {
        if (!$this->_shopCategories) {
            $shop = Mage::helper('gri_catalogcustom')->getStoreShop();
            $this->_shopCategories = $this->getCategoriesByUrlKeys($shop , $store , $level);
        }
        return $this->_shopCategories;
    }

    /**
     * @param Mage_Core_Model_Store $store
     * @return Mage_Catalog_Model_Resource_Category_Collection
     */
    public function getBrandShopCategories($store)
    {
        if (!$this->_shopCategories) {
            $shop = Mage::helper('gri_catalogcustom')->getStoreShop();

            $this->_shopCategories = $this->getCategoriesByUrlKeys($shop , $store, 4 );
        }
        return $this->_shopCategories;
    }


    /**
     * @param Mage_Core_Model_Store $store
     * @return Mage_Catalog_Model_Resource_Category_Collection
     */
    public function getDynCategories($store ,$level = 2)
    {
        if (!$this->_dynCategories) {
            $dynCategoryKeys = Mage::helper('gri_catalogcustom')->getDynCategoryUrlKeys();
            $this->_dynCategories = $this->getCategoriesByUrlKeys($dynCategoryKeys, $store,$level);
        }
        return $this->_dynCategories;
    }


    /**
     * @param array $urlKeys
     * @param Mage_Core_Model_Store $store
     * @return Mage_Catalog_Model_Resource_Category_Collection
     */
    public function getCategoriesByUrlKeys(array $urlKeys, $store, $level = 2)
    {
        !$store && $store = Mage::app()->getStore();
        /* @var $categories Mage_Catalog_Model_Resource_Category_Collection */
        $categories = Mage::getModel('catalog/category')->getCollection()->setStoreId($store->getId());
        $categories->addAttributeToSelect('*')
            ->addAttributeToFilter('url_key', array('in' => $urlKeys))
            ->addAttributeToFilter('level', $level);
        return $categories;
    }

    /**
     * @param Mage_Catalog_Model_Category $parent
     * @param $urlKey
     * @param $storeId
     * @return false|Mage_Catalog_Model_Category
     */
    public function getCategory(Mage_Catalog_Model_Category $parent, $urlKey, $storeId = 0)
    {
        if (!isset($this->_categories[$key = $parent->getPath() . '-' . $urlKey])) {
            $this->_categories[$key] = FALSE;
            /* @var $categories Mage_Catalog_Model_Resource_Category_Collection */
            $categories = Mage::getSingleton('catalog/category')->getCollection()->setStoreId($storeId);
            $categories->addAttributeToSelect('url_key')
                ->addFieldToFilter('url_key', $urlKey)
                ->addFieldToFilter('parent_id', $parent->getId())
                ->setPageSize(1);
            $categories->count() and $this->_categories[$key] = $categories->getFirstItem();
        }
        return $this->_categories[$key];
    }

    public function getRootCategoryUrlKey(Mage_Catalog_Model_Category $category){
       $path = explode('/',$category->getPath());
       $path = count($path) > 2  ? $path[2] : 0;
       $rootCategoryId = $path;
       if( !isset($this->_urlKeys[$rootCategoryId]) && $rootCategoryId ){
          $category = Mage::getSingleton('catalog/category')->unsetData()->load($rootCategoryId);
          $this->_urlKeys[$rootCategoryId] = $category->getUrlKey();
       }

       return $this->_urlKeys[$rootCategoryId];
    }
}
