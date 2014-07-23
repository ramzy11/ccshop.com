<?php

class Gri_CatalogCustom_Model_Category extends Mage_Catalog_Model_Category
{

    /**
     * @return Gri_CatalogCustom_Helper_Category
     */
    public function getCategoryHelper() {
        return Mage::helper('gri_catalogcustom/category');
    }

    /**
     * @return Mage_Catalog_Helper_Data
     */
    public function getHelper() {
        return Mage::helper('catalog');
    }

    /**
     * Get category products collection
     *
     * @return Gri_CatalogCustom_Model_Category
     */
    protected function getFilterCategroy() {
        if ($this->getBrandCategory()) {
            return $this->getBrandCategory();
        } else if ($this->getShopCategory()) {
            return $this->getShopCategory();
        }
        return FALSE;
    }

    public function getEditorsPickCollection($count = null) {
        $productCollection = array();
        $visibility = array(
            Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
            Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG
        );
        if (!$filterCategory = $this->getFilterCategroy())
            return $productCollection;
        $collection = Mage::getResourceModel('catalog/product_collection')
                ->setStoreId($this->getStoreId())
                ->addCategoryFilter($filterCategory)
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('visibility', $visibility)
                ->addAttributeToFilter('editors_pick', array('gt' => '0'))
                ->addAttributeToSort('editors_pick', 'desc');

        //$collection->setSize(count($collection));
        return $collection;
        //$collection->getSelect()->order('rand()');
        //if($count) $collection->setPage(1, $count);
        /*
          $originalData = array();
          $editorPickData =array();
          foreach($collection as $product) {
          $originalData[$product->getId()] = $product;
          $editorPickData[$product->getId()] = $product->getData('editors_pick');
          }
          arsort($editorPickData);
          foreach($editorPickData as $k=>$v) $productCollection[$k] = $originalData[$k];
          $productCollection->setSize(count($productCollection));
          return $productCollection;
         */
    }

    /**
     * @param string $path
     * @return integer|FALSE
     */
    public function getIdByUrlPath($path)
    {
        if ($this->getData('id_by_url_path', $path) === NULL) {
            $ids = (array)$this->getData('id_by_url_path');
            $pathArray = explode('/', $path);
            $urlKeyAttribute = $this->getResource()->getAttribute('url_key');
            if (!$stmt = $this->getStmtIdByUrlPath()) {
                $select = $this->getResource()->getReadConnection()->select();
                $select->from(array('u' => $urlKeyAttribute->getBackendTable()), array('entity_id'))
                    ->join(array('e' => $this->getResource()->getTable($urlKeyAttribute->getEntity()->getEntityTable())), 'e.entity_id = u.entity_id', array())
                    ->where('u.attribute_id = ?', $urlKeyAttribute->getAttributeId())
                    ->where('u.store_id = 0');
                $stmt = $this->getResource()->getReadConnection()->prepare($select . ' AND u.value = :url_key AND e.parent_id = :parent_id');
                $this->setStmtIdByUrlPath($stmt);
            }
            $parentId = Mage::app()->getStore()->getRootCategoryId();
            $id = FALSE;
            foreach ($pathArray as $urlKey) {
                $stmt->execute(array(
                    ':url_key' => $urlKey,
                    ':parent_id' => $parentId,
                ));
                $id = $stmt->fetchColumn();
                if (!$parentId = $id) {
                    $id = FALSE;
                    break;
                }
            }
            $ids[$path] = $id;
            $this->setData('id_by_url_path', $ids);
        }
        return $this->getData('id_by_url_path', $path);
    }

    /**
     * Get catalog layer model
     *
     * @return Mage_Catalog_Model_Layer
     */
    public function getLayer() {
        $layer = Mage::registry('current_layer');
        if ($layer) {
            return $layer;
        }
        return Mage::getSingleton('catalog/layer');
    }

    public function getMetaDescription() {
        if (!$this->getData('meta_description')) {
            $description = array();
            foreach ($this->getHelper()->getBreadcrumbPath() as $v) {
                $description[] = $v['label'];
            }
            $this->setData('meta_description',
                Mage::getStoreConfig(Mage_Core_Model_Store::XML_PATH_STORE_STORE_NAME) . ' - ' . implode(', ', $description));
        }
        return $this->getData('meta_description');
    }

    public function getMetaKeywords() {
        if (!$this->getData('meta_keywords')) {
            $keywords = array();
            foreach ($this->getHelper()->getBreadcrumbPath() as $v) {
                $keywords[] = $v['label'];
            }
            $this->setData('meta_keywords', implode(',', $keywords));
        }
        return $this->getData('meta_keywords');
    }

    public function getNewArrivalCollection() {
        $collection = Mage::getResourceModel('catalog/product_collection');
        $todayStartOfDayDate = Mage::app()->getLocale()->date()
                ->setTime('00:00:00')
                ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

        $todayEndOfDayDate = Mage::app()->getLocale()->date()
                ->setTime('23:59:59')
                ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
        $visibility = array(
            Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
            Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG
        );

        // for New Arrival category
        if (!$filterCategory = $this->getFilterCategroy()) {
            if (strtolower($this->getName()) == 'new arrivals' && $this->getLevel() == 2) {
                $filterCategory = 'root';
            }
            if (strtolower($this->getParentCategory()->getName()) == 'new arrivals') {
                $filterCategory = $this->getResourceCollection()
                                ->addAttributeToFilter('url_key', $this->getData('url_key'))
                                ->addAttributeToFilter('level', 2)->getFirstItem();
            }
        }
        if (!$filterCategory)
            return $collection->addAttributeToFilter('entity_id', 0);
        $collection->setStoreId($this->getStoreId())
                //->addCategoryFilter($filterCategory)
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('visibility', $visibility)
                ->addAttributeToFilter('news_from_date', array('or' => array(
                        0 => array('date' => true, 'to' => $todayEndOfDayDate),
                        1 => array('is' => new Zend_Db_Expr('null')))
                        ), 'left')
                ->addAttributeToFilter('news_to_date', array('or' => array(
                        0 => array('date' => true, 'from' => $todayStartOfDayDate),
                        1 => array('is' => new Zend_Db_Expr('null')))
                        ), 'left')
                ->addAttributeToFilter(
                        array(
                            array('attribute' => 'news_from_date', 'is' => new Zend_Db_Expr('not null')),
                            array('attribute' => 'news_to_date', 'is' => new Zend_Db_Expr('not null'))
                        )
                )
                ->addAttributeToSort('news_from_date', 'desc');
        if ($filterCategory != 'root')
            $collection->addCategoryFilter($filterCategory);
        return $collection;
    }

    public function getBestSellerCollection($count = null) {
        $collection = parent::getProductCollection();
        $collection->addAttributeToSelect('image')
            ->addAttributeToFilter(array(
                array('attribute' => 'best_seller', 'gt' => 0),
                array('attribute' => 'qty_ordered', 'gt' => 0),
            ))
            ->addAttributeToSort('best_seller', $collection::SORT_ORDER_DESC)
            ->addAttributeToSort('qty_ordered', $collection::SORT_ORDER_DESC)
            ->addAttributeToSort('entity_id', $collection::SORT_ORDER_DESC);
        $layer = $this->getLayer();
        $layer->prepareProductCollection($collection);

        return $collection;
    }

    /**
     * Get brand category for current category
     * @return Gri_CatalogCustom_Model_Category
     */
    public function getBrandCategory() {
        if ($this->getData('brand_category') === NULL) {
            $allBrandIds = array();
            $brands = $this->getCategoryHelper()->getBrandCategories($this->getStore());
            foreach ($brands as $v)
                $allBrandIds[] = $v->getId();
            $currentBrandCategoryId = array_intersect($allBrandIds, explode('/', $this->getPath()));
            $category = FALSE;
            if ($currentBrandCategoryId) {
                $category = $brands->getItemById(reset($currentBrandCategoryId))->setStoreId($this->getStoreId())->load(NULL);
            }
            $this->setData('brand_category', $category);
        }
        return $this->getData('brand_category');
    }

    public function getShopCategory() {
        if ($this->getData('shop_category') === NULL) {
            $allShopIds = array();
            $shops = $this->getCategoryHelper()->getShopCategories($this->getStore()); //Root:  shoes/bags/clothing/accessories
            foreach ($shops as $v)
                $allShopIds[] = $v->getId();
            $currentShopCategoryId = array_intersect($allShopIds, explode('/', $this->getPath()));
            $category = FALSE;
            if ($currentShopCategoryId) {
                $category = $shops->getItemById(reset($currentShopCategoryId))->setStoreId($this->getStoreId())->load(NULL);
            }
            $this->setData('shop_category', $category);
        }
        return $this->getData('shop_category');
    }

    public function getBrandShopCategory()
    {
        if ($this->getData('brand_shop_category') === NULL) {
            $allShopIds = array();
            $shops = $this->getCategoryHelper()->getBrandShopCategories($this->getStore()); //Root:  shoes/bags/clothing/accessories
            foreach ($shops as $v)
                $allShopIds[] = $v->getId();
            $currentShopCategoryId = array_intersect($allShopIds, explode('/', $this->getPath()));
            $category = FALSE;
            if ($currentShopCategoryId) {
                $category = $shops->getItemById(reset($currentShopCategoryId))->setStoreId($this->getStoreId())->load(NULL);
            }
            $this->setData('brand_shop_category', $category);
        }
        return $this->getData('brand_shop_category');
    }


    public function getDynCategory()
    {
        if ($this->getData('dyn_category') === NULL) {
            $allDynIds = array();
            $dynCategories = $this->getCategoryHelper()->getDynCategories($this->getStore()); //Root: new-arrivals
            foreach ($dynCategories as $v)
                $allDynIds[] = $v->getId();
            $currentDynCategoryId = array_intersect($allDynIds, explode('/', $this->getPath()));
            $category = FALSE;
            if ($currentDynCategoryId) {
                $category = $dynCategories->getItemById(reset($currentDynCategoryId))->setStoreId($this->getStoreId())->load(NULL);
            }
            $this->setData('dyn_category', $category);
        }
        return $this->getData('dyn_category');
    }

    /**
     * Get CURRENT category is Dyn
     * @param Mage_Catalog_Model_Category $category
     * @return int|mixed
     */
    public function getIsDynCategory()
    {
        $sql = "SELECT * FROM `catalog_category_dynamic_product_index` WHERE `category_id`='".$this->getId()."'";
        return $this->_getReadAdapter()->query($sql)->rowCount() ? TRUE : FALSE;
    }

    /**
     * @return Varien_Db_Adapter_Pdo_Mysql
     */
    protected function _getReadAdapter()
    {
        $resource = Mage::getSingleton('core/resource');
        return   $resource->getConnection('core_read');
    }

    public function getPreOrderCollection()
    {
        /* @var $collection Mage_Catalog_Model_Resource_Product_Collection */
        $collection = Mage::getResourceModel('catalog/product_collection');
        $todayStartOfDayDate = Mage::app()->getLocale()->date()
            ->setTime('00:00:00')
            ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

        $todayEndOfDayDate = Mage::app()->getLocale()->date()
            ->setTime('23:59:59')
            ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
        $visibility = array(
            Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
            Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG
        );

        if (!$filterCategory = $this->getFilterCategroy())
            return $collection->addAttributeToFilter('entity_id', 0);
        $collection->setStoreId($this->getStoreId())
            ->addCategoryFilter($filterCategory)
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('visibility', $visibility)
            ->addAttributeToFilter('preorder_from_date', array('or' => array(
                0 => array('date' => true, 'to' => $todayEndOfDayDate),
                1 => array('is' => new Zend_Db_Expr('null')))
            ), 'left')
            ->addAttributeToFilter('preorder_to_date', array('or' => array(
                0 => array('date' => true, 'from' => $todayStartOfDayDate),
                1 => array('is' => new Zend_Db_Expr('null')))
            ), 'left')
            ->addAttributeToFilter(
                array(
                    array('attribute' => 'preorder_from_date', 'is' => new Zend_Db_Expr('not null')),
                    array('attribute' => 'preorder_to_date', 'is' => new Zend_Db_Expr('not null'))
                )
            )
            ->addAttributeToSort('entity_id', 'desc');
        return $collection;
    }

    public function getPreSalesCollection()
    {
        /* @var $collection Mage_Catalog_Model_Resource_Product_Collection */
        $collection = Mage::getResourceModel('catalog/product_collection');
        $visibility = array(
            Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
            Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG
        );

        if (!$filterCategory = $this->getFilterCategroy())
            return $collection->addAttributeToFilter('entity_id', 0);
        $collection->setStoreId($this->getStoreId())->addPriceData()
            ->addCategoryFilter($filterCategory)
            ->addAttributeToSelect('*')
//            ->addAttributeToFilter('visibility', $visibility)
            ->addAttributeToSort('entity_id', 'desc');
        /* @var $preSaleModel Gri_Presale_Model_Rule */
        $preSaleModel = Mage::getSingleton('gri_presale/rule');
        $productIds = $preSaleModel->getProductIds($filterCategory);
        $collection->addAttributeToFilter('entity_id', array('in' => $productIds));
        return $collection;
    }

    /**
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function getProductCollection()
    {
        if ($this->getData('product_collection') === NULL) {
            $collection = FALSE;
            if (strtolower($this->getData('display_mode')) == 'page' && $landing_page = $this->getData('landing_page')) {
                $cmsBlock = Mage::getModel('cms/block')->load($landing_page);
                $identifier = $cmsBlock->getIdentifier();

                Mage::registry('special_category') or Mage::register('special_category', $identifier);
                if (strpos($identifier, 'editors_pick') !== FALSE) $collection = $this->getEditorsPickCollection();
                else if (strpos($identifier, 'best_seller') !== FALSE) $collection = $this->getParentCategory()->getBestSellerCollection();
                else if (strpos($identifier, 'new-arrivals') !== FALSE) $collection = $this->getNewArrivalCollection();
                else if (strpos($identifier, 'pre-order') !== FALSE) $collection = $this->getPreOrderCollection();
                else if (strpos($identifier, 'pre-sales') !== FALSE) $collection = $this->getPreSalesCollection();
            }
            if (!$collection) $collection = parent::getProductCollection();
            $this->setData('product_collection', $collection);
        }
        return $this->getData('product_collection');
    }
}
