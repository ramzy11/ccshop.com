<?php

/**
 * Class Gri_CatalogCustom_Model_Product
 * @method array getSimplePrices()
 * @method Gri_CatalogInventory_Model_Stock_Item|NULL getStockItem()
 * @method boolean getUseDummyFinalPrice()
 * @method Gri_CatalogCustom_Model_Product setUseDummyFinalPrice(boolean $value)
 */
class Gri_CatalogCustom_Model_Product extends Mage_Catalog_Model_Product
{
    const PRPCESS_REINDEX_ID = 2;
    const IS_ALL_GROUPS = 0;
    protected $_idBySku = array();
    protected $_mayLikeCollection = NULL;
    protected $_attributeSetNames = array();

    protected function _beforeSave()
    {
        parent::_beforeSave();
        //$this->setOnSale($this->getData('price') > $this->getSpecialPrice() ? 1 : 0);
        return $this;
    }

    public function getBrandProductIds()
    {
        /* @var $cache Mage_Core_Model_Cache */
        $cache = Mage::getSingleton('core/cache');
        $result = $cache->load('brandProductIds');
        if ($result === FALSE) {
            $productCollection = $this->getCollection()
                ->addAttributeToFilter('brand', array('gt' => ''))
                ->addFieldToFilter('type_id', array(
                    Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE,
                    Mage_Catalog_Model_Product_Type::TYPE_SIMPLE,
                ))->addPriceData();
            Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($productCollection);
//            Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($productCollection);
            $select = $productCollection->getSelect();
            $data = array();
            foreach ($select->query()->fetchAll() as $v) {
                $data[$v['brand']][] = $v['entity_id'];
            }
            $cache->save($result = serialize($data), 'brandProductIds', array('brandProductIds'), 43200);
        }
        return unserialize($result);
    }

    public function getFinalPrice($qty = NULL)
    {
        Mage::dispatchEvent('catalog_product_before_get_final_price', array('product' => $this, 'qty' => $qty));
        return parent::getFinalPrice($qty);
    }

    public function getGroupedProductsPrices()
    {
        if ($this->getData('grouped_products_price') === NULL) {
            $price = array();
            $type = $this->getTypeInstance()->setProduct($this);
            if ($type instanceof Mage_Catalog_Model_Product_Type_Grouped) {
                foreach ($type->getAssociatedProducts() as $_product) {
                    $price[] = $_product->getFinalPrice();
                }
            } else
                $price[] = $this->getFinalPrice();
            $this->setData('grouped_products_price', $price);
        }
        return $this->getData('grouped_products_price');
    }

    public function getMaxPrice()
    {
        return $this->getTypeInstance() instanceof Mage_Catalog_Model_Product_Type_Grouped ?
            max($this->getGroupedProductsPrices()) : $this->getFinalPrice();
    }

    public function getMinimalPrice()
    {
        return $this->getTypeInstance() instanceof Mage_Catalog_Model_Product_Type_Grouped ?
            min($this->getGroupedProductsPrices()) : parent::getMinimalPrice();
    }

    public function getMetaDescription()
    {
        if (!$this->getData('meta_description')) {
            $description = array(
                $this->getName(),
                $this->getDescription(),
            );
            $this->setData('meta_description', implode(' - ', $description));
        }
        return $this->getData('meta_description');
    }

    public function getMetaKeyword()
    {
        if (!$this->getData('meta_keyword')) {
            $keyword = array(
                $this->getName(),
                $this->getSku(),
            );
            $this->getCategory() and $keyword[] = $this->getCategory()->getName();
            $this->setData('meta_keyword', implode(',', $keyword));
        }
        return $this->getData('meta_keyword');
    }

    public function getPriceInCurrentCurrency()
    {
        if ($this->_getData($key = 'price_in_current_currency') === NULL) {
            $price = Mage::app()->getStore()->convertPrice($this->getPrice());
            $this->setData($key, $price);
        }
        return $this->_getData($key);
    }

    public function getRewardPoints()
    {
        if ($this->getTypeInstance() instanceof Gri_GiftCard_Model_Catalog_Product_Type_Giftcard) {
            $this->getPriceModel()->getRewardPoints(1, $this);
        }
        return $this->getData('reward_points');
    }

    public function getUpSellProductCollection($count = NULL)
    {
        $collection = parent::getUpSellProductCollection();
        if (Mage::app()->getLayout()->getArea() == Mage_Core_Model_App_Area::AREA_ADMINHTML || $collection->count())
            return $collection;
        $collection = $this->getCollection();
        if (!$brand = $this->getData('brand'))
            return $collection->addAttributeToSelect('entity_id')->addFieldToFilter('entity_id', 0);
        else {
            $ids = $this->getBrandProductIds();
            if (!isset($ids[$brand]))
                return $collection->addAttributeToSelect('entity_id')->addFieldToFilter('entity_id', 0);
            $upSellProductIds = $ids[$brand];
            $id2Keys = array_flip($upSellProductIds);
            if (isset($id2Keys[$this->getId()]))
                unset($upSellProductIds[$id2Keys[$this->getId()]]);
            shuffle($upSellProductIds);
            $count = min(max($count * 2, 30), count($upSellProductIds));
            array_splice($upSellProductIds, $count);
        }
        return $collection->addAttributeToSelect('*')->addFieldToFilter('entity_id', array('in' => $upSellProductIds));
    }

    public function isGiftCard()
    {
        return $this->getTypeId() == 'giftcard';
    }

    /**
     *  Add Product Price
     *
     */
    public function addGroupPrice()
    {
        $groupPrice = array();
        $vipDiscounts = $this->getVipDiscounts();

        foreach ($vipDiscounts as $groupId => $discount) {
            $groupPrice[] = array(
                'cust_group' => $groupId,
                'price' => $this->getPrice() * $discount,
                'website_id' => $this->getWebsiteId(),
            );

            $this->setGroupPrice($groupPrice);
        }
    }

    protected function getVipDiscounts()
    {
        return array($this->getGroupIdByVipLevel('offlinevip') => 1 - $this->getLevelDiscount('offlinevip') / 100,
            $this->getGroupIdByVipLevel('silver') => 1 - $this->getLevelDiscount('silver') / 100,
            $this->getGroupIdByVipLevel('gold') => 1 - $this->getLevelDiscount('gold') / 100,
        );
    }

    /**
     *  Update All  Products' Group Price
     *
     */
    public function updateAllProductsVipGroupPrice()
    {
        $groupPrice = array();
        $productCollection = Mage::getsingleton('catalog/product')->getResourceCollection()
            ->joinAttribute('price', 'catalog_product/price', 'entity_id')
            ->addAttributeToFilter('visibility', array('in' => array(Mage_Catalog_Model_Product_Visibility :: VISIBILITY_BOTH,
                Mage_Catalog_Model_Product_Visibility :: VISIBILITY_IN_CATALOG,
            )));

        $select = $productCollection->getSelect();
        if ($data = $select->query(PDO::FETCH_ASSOC)->fetchAll()) {
            try {
                // empty table
                $adapter = $this->_getWriteAdapter();
                $adapter->query('Delete  From   ' . $this->getGroupPriceTablePriceTableName());
                $vipDiscounts = $this->getVipDiscounts();
                $adapter->beginTransaction();

                foreach ($data as $_data) {
                    foreach ($vipDiscounts as $groupId => $discount) {
                        $groupPrice[] = array('entity_id' => $_data['entity_id'],
                            'all_groups' => self::IS_ALL_GROUPS,
                            'customer_group_id' => $groupId,
                            'value' => round($_data['price'] * $discount, 1),
                            'website_id' => $this->getWebsiteId()
                        );
                    }

                    $adapter->insertOnDuplicate($this->getGroupPriceTablePriceTableName(), $groupPrice, array(
                        'customer_group_id',
                        'value',
                        'entity_id',
                        'all_groups',
                    ));
                }

                $adapter->commit();
                //reindex price
                $this->reindexPrice();
                return TRUE;
            } catch (Exception $e) {
                Mage::logException($e);
                return FALSE;
            }
        }
        return TRUE;
    }

    public function reindexPrice()
    {
        $process = Mage::getModel('index/process')->load(self::PRPCESS_REINDEX_ID);
        if (!$process->getId()) {
            return FALSE;
        }

        try {
            Varien_Profiler::start('__INDEX_PROCESS_REINDEX_ALL__');
            $process->reindexEverything();
            Varien_Profiler::stop('__INDEX_PROCESS_REINDEX_ALL__');
        } catch (Exception $e) {
            Mage::logException($e);
            return FALSE;
        }
        return TRUE;
    }

    /**
     *  Getter Admin Store Websiteid
     *
     */
    public function getWebsiteId()
    {
        return Mage::app()->getStore('admin')->getWebsiteId();
    }

    /**
     *  Getter Write adapter
     *
     */
    protected function _getWriteAdapter()
    {
        return Mage::getSingleton('core/resource')->getConnection('core_write');
    }

    protected function getLevelDiscount($groupCode)
    {
        return Mage::helper('gri_vip')->getLevelDiscount($groupCode);
    }

    /**
     *
     */
    protected function getGroupIdByVipLevel($code)
    {
        return Mage::helper('gri_vip')->getGroupIdByVipLevel($code);
    }

    protected function _getVipHelper()
    {
        return Mage::helper('gri_vip');
    }

    /**
     *  Getter
     *
     * @return string | null
     */
    protected function getGroupPriceTablePriceTableName()
    {
        return Mage::getModel('core/resource')->getTableName('catalog/product_attribute_group_price');
    }

    public function getIdBySku($sku)
    {
        if (!isset($this->_idBySku[$sku])) {
            $id = parent::getIdBySku($sku);
            $id or $id = FALSE;
            $this->_idBySku[$sku] = $id;
        }
        return $this->_idBySku[$sku];
    }

    /**
     * Getter CatalogProductTableName
     * @return string
     */
    protected function getCatalogProductTableName()
    {
        return Mage::getModel('core/resource')->getTableName('catalog/product');
    }

    /**
     *   getVIPLevels
     * @return Array
     */
    protected function getVIPLevels()
    {
        return Mage::helper('gri_vip')->getVIPLevels();
    }


    /**
     * Retrieve type instance
     *
     * Type instance implement type depended logic
     *
     * @param  bool $singleton
     * @return Mage_Catalog_Model_Product_Type_Abstract
     */
    public function getTypeInstance($singleton = false)
    {
        if ($singleton === true) {
            if (is_null($this->_typeInstanceSingleton)) {
                $this->_typeInstanceSingleton = Mage::getSingleton('catalog/product_type')
                    ->factory($this, true);
            }
            return $this->_typeInstanceSingleton;
        }

        //  if ($this->_typeInstance === null) {
        $this->_typeInstance = Mage::getSingleton('catalog/product_type')
            ->factory($this);
        //  }
        return $this->_typeInstance;
    }


    public function getCurrentCategoryProductCollection($limit = 0 )
    {
        if( $this->_mayLikeCollection == NULL) {
            if($currentCategoryId = $this->getCategoryIdFromBreadcrumbPath()){
                $categoryIds = array($currentCategoryId);
            }

            $youMayLikeProductIds = array();
            foreach($categoryIds as $cid) {
                /* @var $category Gri_CatalogCustom_Model_Category */
                $category = Mage::getSingleton('catalog/category')->unsetData()->load($cid);

                /* @var $productCollection Mage_Catalog_Model_Resource_Product_Collection */
                $productCollection = $category->getProductCollection()
                                         ->addAttributeToSelect('*')
                                         ->addAttributeToFilter('type_id', Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE)
                                         ->addAttributeToFilter('entity_id', array('neq'=> $this->getId()));

                Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($productCollection);

                $productIds = $productCollection->getAllIds();
                $youMayLikeProductIds = array_merge($youMayLikeProductIds, $productIds);
            }

            $youMayLikeProductIds = array_unique($youMayLikeProductIds);
            shuffle($youMayLikeProductIds); // random  array
            $youMayLikeProductIds  =  $limit ? array_slice($youMayLikeProductIds, 0, $limit) : $youMayLikeProductIds;
            $this->_mayLikeCollection =  Mage::getSingleton('catalog/product')->getCollection()->addAttributeToSelect('*')->addFieldToFilter('entity_id', array('in' => $youMayLikeProductIds));
        }

        return $this->_mayLikeCollection;
    }

    /**
     *  Retrieve Final Category ID
     *  @return int
     */
    public function getCategoryIdFromBreadcrumbPath()
    {
        $keys = array_keys( Mage::helper('catalog')->getBreadcrumbPath() );
        $categoryIds = $this->getCategoryIds();
        $categoryId = Mage::app()->getStore()->getRootCategoryId();

        if(count($keys)) {
            foreach($keys as $key) {
                if( strpos($key, 'category') !== FALSE){
                    $key = intval(trim(str_replace('category', '', $key)));
                    if($key > $categoryId){
                        $categoryId = $key;
                    }
                }
            }
        }
        return $categoryId ;
    }

    public function getAttributeSetName()
    {
        $attributeSetName = '';
        if($this->getAttributeSetId()){
            $attributeSetName = Mage::getSingleton('eav/entity_attribute_set')->unsetData()
                ->load($this->getAttributeSetId())->getAttributeSetName();
        }
        return $attributeSetName;
    }

    public function getIsFlashSale()
    {
        /* @var $flashSale Gri_FlashSale_Model_FlashSale */
        $flashSale = Mage::getSingleton('gri_flashsale/flashSale');
        return ($this->getId() && $flashSale->getIsFlashSaleProductByParentId($this->getId()) !== FALSE) ? TRUE : FALSE;
    }
}
