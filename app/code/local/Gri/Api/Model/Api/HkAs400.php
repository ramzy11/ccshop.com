<?php

class Gri_Api_Model_Api_HkAs400 extends Mage_Api_Model_Resource_Abstract
{
    const CONST_LOG_EXCEPTION_NEW_PRODUCT = 'hkas400.new.product.log';
    const CONST_LOG_EXCEPTION_UPDATE_PRODUCT = 'hkas400.update.product.log';
    const CONST_LOG_EXCEPTION_INDEX_PRODUCT = 'hkas400.index.exception.log';
    const CONST_LOG_EXCEPTION_UPDATE_PRICES = 'hkas400.update.prices.log';
    const CONST_LOG_EXCEPTION_ARCHIVE_PRODUCT = 'hkas400.archive.product.log';
    const CONST_LOG_EXCEPTION_UPDATE_ORDER_STATUS = 'hkas400.update.order.status.log';
    const CONST_LOG_EXCEPTION_UPDATE_INVENTORY = 'stock.log';

    protected $_datetimeFormat = 'yyyyMMddHHmmss';
    protected $_parentIds = array();
    protected $_orderFieldsMapping = array(
        'orderId' => 'increment_id',
        'createTime' => 'create_time',
        'status' => 'status',
        'customerId' => 'customer_id',
        'customerName' => 'customer_name',
        'customerEmail' => 'customer_email',
        'customerGroup' => 'customer_group',
        'customerGender' => 'customer_gender',
        'subTotal' => 'subtotal',
        'discountAmount' => 'discount_amount',
        'shippingAmount' => 'shipping_amount',
        'grandTotal' => 'grand_total',
        'totalPaid' => 'total_paid',
        'itemCounts' => 'total_item_count',
        'totalQty' => 'total_qty_ordered',
        'weight' => 'weight',
        'paymentMethod' => 'payment_method',
        'paymentTransactionId' => 'payment_transaction_id',
        'payTime' => 'pay_time',
        'recipient' => 'recipient',
        'shippingMethod' => 'shipping_method',
        'shippingAddress' => 'shipping_address',
        'postcode' => 'postcode',
        'telephone' => 'telephone',
        'mobile' => 'mobile',
        'fapiao_title' => 'fapiao',
        'fapiao_type' => 'fapiao_type',
        'remarks' => 'remarks',
        'orderItems' => 'order_items',
    );
    protected $_orderItemFieldsMapping = array(
        'orderItemId' => 'item_id',
        'sku' => 'sku',
        'productName' => 'name',
        'price' => 'original_price',
        'weight' => 'weight',
        'qty' => 'qty_ordered',
        'subTotal' => 'row_total',
        'discountAmount' => 'discount_amount',
        'rowWeight' => 'row_weight',
        'rowTotal' => 'final_row_total',
        'options' => 'options',
    );
    protected $_orderStateNotReady = array(
        Mage_Sales_Model_Order::STATE_NEW,
        Mage_Sales_Model_Order::STATE_CANCELED,
        Mage_Sales_Model_Order::STATE_CLOSED,
        Mage_Sales_Model_Order::STATE_HOLDED,
    );

    protected $_statusOptions = array();

    public function __construct()
    {
       //  $this->_getAdapter()->setFaults($this->_getConfig()->getFaults());
    }

    protected function _fault($code, $customMessage = NULL, $terminate = TRUE)
    {
        if ($terminate) parent::_fault($code, $customMessage);
        else {
            $this->_getAdapter()->addError($code, $customMessage);
        }
    }

    /**
     * @return Gri_Api_Model_Api_HkAs400_Adapter_Rest
     */
    protected function _getAdapter()
    {
        return $this->_getServer()->getAdapter();
    }

    protected function _mapFields(Varien_Object $object, array $fieldsMappingTable)
    {
        $result = array();
        foreach ($fieldsMappingTable as $k => $v) {
            $result[$k] = $object->getData($v);
        }
        return $result;
    }

    /**
     * @return Varien_Db_Adapter_Pdo_Mysql
     */
    protected function _getDbConnection($write = true)
    {
        $resource = Mage::getSingleton('core/resource');
        return  $write ? $resource->getConnection('core_write') : $resource->getConnection('core_read');
    }

    protected function storeApiProductsIntoDatabase($transactionId, $products, $type, $store = 'admin')
    {
        $timestamp = Varien_Date::now();
        $data = array();
        $table = $this->getTableName('gri_api_product');
        $initialData = array (
            'transaction_id' => $transactionId,
            'type' => $type,
            'status' => 0,
            'created_at' => $timestamp,
        );

        $this->replaceOriginalPriceWithMaxOriginalPrice($products);
        foreach ($products as $v) {
            /* Mapping New Fields */
            $v['name'] = $v['product_name'];
            !isset($v['color_filter_1']) && $v['color_filter_1'] = $v['color_1'];
            !isset($v['color_filter_2']) && $v['color_filter_2'] = $v['color_2'];
            $v['attribute_set'] = $v['product_type'];
            !isset($v['configurable']) && $v['configurable'] = 1;
            $v['store'] = $store;
            !isset($v['ref_no']) && $v['ref_no'] = $v['reference'];
            if($type == 'new'){
                !isset($v['is_archived']) && $v['is_archived'] = false;
            }

            $data[] = array_merge($initialData, array('json' => Zend_Json_Encoder::encode($v)));
        }
        $this->_getDbConnection()->insertMultiple($table, $data);
        return true;
    }

    /**
     * @param $products
     * @return array
     */
    protected function replaceOriginalPriceWithMaxOriginalPrice( &$products )
    {
        $obj = new Varien_Object();
        $prices = array();
        $specialPrices = array();
        foreach ($products as $row) {
            if (empty($row['style_name'])) continue;
            $obj->setData($row);
            $key = $obj->getData('style_name');
            $price = $obj->getData('price');
            $specialPrice = $obj->getData('special_price');
            isset($prices[$key]) or $prices[$key] = $price;
            isset($specialPrices[$key]) or $specialPrices[$key] = $specialPrice;
            $prices[$key] = max($prices[$key], $price);
            $specialPrices[$key] = max($specialPrices[$key], $specialPrice);
        }
        foreach ($products as &$row) {
            if (empty($row['style_name']) || !isset($prices[$key = $row['style_name']])) continue;
            $row['max_price'] = $prices[$key];
            $row['max_special_price'] = $specialPrices[$key] ? $specialPrices[$key] : NULL;
        }
        unset($row);
    }

    /**
     *  P1: New Products
     *  @param $data
     * @return string
     */
    public function newProducts($data)
    {
        Mage::log(var_export($data,true), null, self::CONST_LOG_EXCEPTION_NEW_PRODUCT);
        $store = $this->getStoreNameByLanguage($data['language']);
        $this->storeApiProductsIntoDatabase($data['transaction_id'], $data['products'], 'new', $store);
        return 'received';
    }

    /**
     * P1: Create Products From Cron Job
     * @return $this
     */
    public function newProductsByJob()
    {
        /* @var $importAdapter Gri_ImportData_Model_Convert_Adapter_Product */
        $importAdapter = Mage::getSingleton('gri_importdata/convert_adapter_product');

        Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

        $readAdapter = $this->getReadAdapter();
        $writeAdapter = $this->getWriteAdapter();

        $sql = "SELECT %s FROM `{$this->getTableName('gri_api_product')}` WHERE `status`= 0 AND `type`= 'new'";
        $count = (int)$readAdapter->fetchOne(sprintf($sql, 'COUNT(*)'));
        $sql .= ' LIMIT 20';
        $result = $readAdapter->fetchAll(sprintf($sql, '`id`,`transaction_id`, `json`'));

        $start_time = $_SERVER["REQUEST_TIME"];
        $executed_num = 0;
        foreach($result as $row){
            $set = '';
            try{
                $product = Zend_Json_Decoder::decode($row['json']);
                $current_time = time();
                if($current_time - $start_time > 60){
                    break;
                }
                $importAdapter->saveRow($product);
            }catch( Exception $e){
                // Update Log
                $set = ",`error_info`= '{$e->getMessage()}'";
                Mage::log($e->getMessage(), '7', self::CONST_LOG_EXCEPTION_NEW_PRODUCT);
            }

            $now = Varien_Date::now();
            $sql = "UPDATE `{$this->getTableName('gri_api_product')}` SET `status`= '1', `updated_at`='{$now}' ".$set."  WHERE `id`='".$row['id']."';";
            $writeAdapter->query($sql);

            $executed_num++;
        }

        try{
            if($count && $count == $executed_num ){
                /* @var $indexProcessPrice Mage_Index_Model_Process */
                $indexProcessPrice = Mage::getModel('index/process')->load('catalog_product_price', 'indexer_code');
                $indexProcessPrice->reindexEverything();
            }
        }catch (Exception $e){
            Mage::log($e->getMessage(), 7, self::CONST_LOG_EXCEPTION_INDEX_PRODUCT);
        }

        return $this;
    }

    /**
     * P2: Update Products
     * @param array $data
     * @return string
     */
    public function updateProducts(array $data)
    {
        Mage::log(var_export($data,true), null, self::CONST_LOG_EXCEPTION_UPDATE_PRODUCT);
        $store = $this->getStoreNameByLanguage($data['language']);
        $this->storeApiProductsIntoDatabase($data['transaction_id'], $data['products'], 'update' ,$store);
        return 'received';
    }

    /**
     *  P2: Update Products From Cron Job
     */
    public function updateProductsByJob()
    {
        /* @var $importAdapter Gri_ImportData_Model_Convert_Adapter_Product */
        $importAdapter = Mage::getModel('gri_importdata/convert_adapter_product');
        $importAdapter->setBatchParams(array('mode'=>'update'));

        Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

        $readAdapter = $this->getReadAdapter();
        $writeAdapter = $this->getWriteAdapter();

        $sql = "SELECT * FROM `{$this->getTableName('gri_api_product')}` WHERE `status`= 0 AND `type`='update' LIMIT 20;";
        $result = $readAdapter->query($sql)->fetchAll(PDO::FETCH_ASSOC);

	Mage::log(date("Y-m-d H:i:s")." update Product started with sql = $sql",7,"update_product_message.log");

        try{
            $start_time = $_SERVER["REQUEST_TIME"];
            foreach($result as $row){
                $product = Zend_Json_Decoder::decode($row['json']);

                $set = '';
                try{
                    $current_time = time();
                    if($current_time - $start_time > 60){
                        break;
                    }
                    $importAdapter->saveRow($product);
                }catch( Exception $e){
                    // Update Log
                    $set = ",`error_info`= '{$e->getMessage()}'";
                }

                $now = Varien_Date::now();
                $sql = "UPDATE `{$this->getTableName('gri_api_product')}` SET `status`= 1, `updated_at`='{$now}' {$set} WHERE `id`='".$row['id']."';";
                $writeAdapter->query($sql);

            }
        }catch (Exception $e){
            Mage::log($e->getMessage(), '7',self::CONST_LOG_EXCEPTION_UPDATE_PRODUCT);
        }

        return $this;
    }

    /**
     * P3:  Update Inventory
     * @param  $data
     * @return  string|json
     */
    public function updateInventory($data = array())
    {
        /* @var $resource Mage_Core_Model_Resource */
        $resource = Mage::getSingleton('core/resource');
        $read = $resource->getConnection('core_read');
        $write = $resource->getConnection('core_write');
        /* @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('catalog/product');

        $productTable = $resource->getTableName('catalog/product');
        $stockItemTable = $resource->getTableName('cataloginventory/stock_item');
        $stockStatusTable = $resource->getTableName('cataloginventory/stock_status');
        $stockStatusIdxTable = $resource->getTableName('cataloginventory/stock_status_indexer_idx');


        $skuExecuted = array();
		$write->beginTransaction();
        foreach ($data as $sku => $qty) {
            if ($productId = $product->getIdBySku($sku)) {
                if ($qty > 0) {
                    $sql = "UPDATE `{$stockItemTable}` SET qty=$qty, is_in_stock=1 WHERE product_id=" . $productId;
                    $write->query($sql);

                    $query = "SELECT * FROM `{$stockStatusTable}` WHERE product_id=$productId AND website_id=1";
                    $select_qry = $read->query($query);
                    $row = $select_qry->fetch();

                    if (isset($row['product_id']) && !empty($row['product_id'])) {
                         $sql = "UPDATE `{$stockStatusTable}` SET qty=$qty, stock_status=1 WHERE product_id=$productId AND website_id=1";
                         $write->query($sql);
                    } else {
                         $sql = "INSERT INTO `{$stockStatusTable}` VALUES($productId,1,1,$qty,1)";
                         $write->query($sql);
                    }

                    $query = "SELECT * FROM `{$stockStatusIdxTable}` WHERE product_id=$productId AND website_id=1";
                    $select_qry = $read->query($query);
                    $row = $select_qry->fetch();

                    if (isset($row['product_id']) && !empty($row['product_id'])) {
                         $sql = "UPDATE `{$stockStatusIdxTable}` SET qty=$qty, stock_status=1 WHERE product_id=$productId AND website_id=1";
                         $write->query($sql);
                    } else {
                         $sql = "INSERT INTO `{$stockStatusIdxTable}` VALUES($productId,1,1,$qty,1)";
                         $write->query($sql);
                    }
                     //Mage::log($row['product_id'] . '-' . $sku . '-' . $qty, NULL, self::CONST_LOG_EXCEPTION_UPDATE_INVENTORY);
                     Mage::log($productId . '-' . $sku . '-' . $qty, NULL, self::CONST_LOG_EXCEPTION_UPDATE_INVENTORY);
                    } else {
                        $sql = "UPDATE `{$stockItemTable}` SET qty=0, is_in_stock=0 WHERE product_id=" . $productId;
                        $write->query($sql);

                        $query = "SELECT * FROM `{$stockStatusTable}` WHERE product_id=$productId AND website_id=1";
                        $select_qry = $read->query($query);
                        $row = $select_qry->fetch();

                        if (isset($row['product_id']) && !empty($row['product_id'])) {
                            $sql = "UPDATE `{$stockStatusTable}` SET qty=0, stock_status=0 WHERE product_id=$productId AND website_id=1";
                            $write->query($sql);
                        } else {
                            $sql = "INSERT INTO `{$stockStatusTable}` VALUES($productId,1,1,0,0)";
                            $write->query($sql);
                        }

                        $query = "SELECT * FROM `{$stockStatusIdxTable}` WHERE product_id=$productId AND website_id=1";
                        $select_qry = $read->query($query);
                        $row = $select_qry->fetch();

                        if (isset($row['product_id']) && !empty($row['product_id'])) {
                            $sql = "UPDATE `{$stockStatusIdxTable}` SET qty=0, stock_status=0 WHERE product_id=$productId AND website_id=1";
                            $write->query($sql);
                        } else {
                            $sql = "INSERT INTO `{$stockStatusIdxTable}` VALUES($productId,1,1,0,0)";
                            $write->query($sql);
                        }
			Mage::log($productId.'-'.$sku.'- 0 stock and set to out of stock',NULL, self::CONST_LOG_EXCEPTION_UPDATE_INVENTORY);
                    }
                    $skuExecuted[] = $sku;
                }
            }
			$write->commit();

            try{
                /* @var $indexProcessPrice Mage_Index_Model_Process */
                //$indexProcessPrice = Mage::getModel('index/process')->load('catalog_product_price', 'indexer_code');
                $indexProcessPrice = Mage::getModel('index/process')->load('cataloginventory_stock', 'indexer_code');
                $indexProcessPrice->reindexEverything();
            }catch (Exception $e){
                Mage::log($e->getMessage(), 7, self::CONST_LOG_EXCEPTION_INDEX_PRODUCT);
            }
        return $skuExecuted;
    }

    /**
     *  P4: Update Prices
     *  @param Array $data
     *  @return string | json
     */
    public function updatePrices($data = array())
    {
        Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
        /* @var $product Gri_CatalogCustom_Model_Product */
        $product = Mage::getModel('catalog/product');

        $skuExecuted = array();

        $prices = array();
        $specialPrices = array();
        foreach ($data as $sku => &$row) {
            $row['price'] = $row['price'] * 1;
            $row['special_price'] = $row['special_price'] * 1;
            if ($productId = $product->getIdBySku($sku)) {
                if ($productParentId = $this->getProductParentId($productId)) {
                    $maxPrice = isset($prices[$productParentId]) ? $prices[$productParentId] : 0;
                    if ($row['price'] > $maxPrice) {
                        $prices[$productParentId] = $row['price'];
                    }
                    $maxSpecialPrice = isset($specialPrices[$productParentId]) ? $specialPrices[$productParentId] : 0;
                    if ($row['special_price'] > $maxSpecialPrice) {
                        $specialPrices[$productParentId] = $row['special_price'];
                    }
                }
            }
        }
        unset($row);

        // Get attributes
        $priceAttribute = $product->getResource()->getAttribute('price');
        $priceAttributeId = $priceAttribute->getAttributeId();
        $specialPriceAttribute = $product->getResource()->getAttribute('special_price');
        $specialPriceAttributeId = $specialPriceAttribute->getAttributeId();
        $discountAttribute = $product->getResource()->getAttribute('discount');
        $discountAttributeId = $discountAttribute->getAttributeId();
        $onSaleAttribute = $product->getResource()->getAttribute('on_sale');
        $onSaleAttributeId = $onSaleAttribute->getAttributeId();
        foreach ($data as $sku => $row) {
            try {
                if ($productId = $product->getIdBySku($sku)) {
                    $this->getWriteAdapter()->beginTransaction();
                    // get Parent Id
                    $productParentId = $this->getProductParentId($productId);

                    $ret = $this->getWriteAdapter()->insertOnDuplicate($priceAttribute->getBackendTable(), array(
                        'entity_type_id' => 4,
                        'attribute_id' => $priceAttributeId,
                        'store_id' => 0,
                        'entity_id' => $productId,
                        'value' => $row['price'],
                    ));
                    if ($productParentId) {
                        $ret = $this->getWriteAdapter()->insertOnDuplicate($priceAttribute->getBackendTable(), array(
                            'entity_type_id' => 4,
                            'attribute_id' => $priceAttributeId,
                            'store_id' => 0,
                            'entity_id' => $productParentId,
                            'value' => $prices[$productParentId],
                        ));

//                      $this->getWriteAdapter()->insertOnDuplicate($specialPriceAttribute->getBackendTable(), array(
//                            'entity_type_id' => 4,
//                            'attribute_id' => $specialPriceAttributeId,
//                            'store_id' => 0,
//                            'entity_id' => $productParentId,
//                            'value' => $specialPrices[$productParentId],
//                      ));
                    }

                    $sql = "select value_id from `{$this->getTableName('catalog_product_entity_decimal')}` WHERE `entity_id`='".$productId."' AND `attribute_id`='".$specialPriceAttributeId."';";

                    if($this->getReadAdapter()->query($sql)->rowCount()){
                        $sql = "UPDATE `{$this->getTableName('catalog_product_entity_decimal')}` SET `value`='".$row['special_price']."' WHERE `entity_id`='".$productId."' AND `attribute_id`='".$specialPriceAttributeId."';";
                    }
                    else {
                        $sql = "INSERT `{$this->getTableName('catalog_product_entity_decimal')}`(`entity_type_id`, `value`, `entity_id`, `attribute_id`,`store_id`) VALUES('4' ,'".$row['special_price']."','$productId','$specialPriceAttributeId','0');";
                    }
                    //$this->getWriteAdapter()->query($sql);
                    $line = $this->getWriteAdapter()->exec($sql);

                    // Update Discount %
                    $sql = "select `value_id` from `{$this->getTableName('catalog_product_entity_varchar')}` WHERE `entity_id`='".$productId."' AND `attribute_id`='".$discountAttributeId."';";
                    if($this->getReadAdapter()->query($sql)->rowCount()){
                        $sql = "UPDATE `{$this->getTableName('catalog_product_entity_varchar')}` SET `value`='".$row['discount']."' WHERE `entity_id` in('".$productId."','". $productParentId ."') AND `attribute_id`='".$discountAttributeId."';";
                    }
                    else {
                        $sql = "
                         INSERT `{$this->getTableName('catalog_product_entity_varchar')}`(`entity_type_id`, `value`, `entity_id`, `attribute_id`,`store_id`) VALUES('4' ,'".$row['discount']."','$productId','$discountAttributeId','0');
                         INSERT `{$this->getTableName('catalog_product_entity_varchar')}`(`entity_type_id`, `value`, `entity_id`, `attribute_id`,`store_id`) VALUES('4' ,'".$row['discount']."','$productParentId','$discountAttributeId','0');
                     ";
                    }
                     //$this->getWriteAdapter()->query($sql);
                    $this->getWriteAdapter()->exec($sql);

                    // Update On Sale
                    $this->getWriteAdapter()->insertOnDuplicate($onSaleAttribute->getBackendTable(), array(
                        'entity_type_id' => 4,
                        'attribute_id' => $onSaleAttributeId,
                        'store_id' => 0,
                        'entity_id' => $productId,
                        'value' => $onSale = ( floatval($row['special_price']) && $row['price'] > $row['special_price'] ? 1 : 0),
                    ));
                    $productParentId and $this->getWriteAdapter()->insertOnDuplicate($onSaleAttribute->getBackendTable(), array(
                        'entity_type_id' => 4,
                        'attribute_id' => $onSaleAttributeId,
                        'store_id' => 0,
                        'entity_id' => $productParentId,
                        'value' => $parentOnSale = ( intval($specialPrices[$productParentId]) && $prices[$productParentId]  > $specialPrices[$productParentId] ? 1 : 0),
                    ));

                    $storeIds = Mage::app()->getWebsite()->getStoreIds();
                    foreach($storeIds as $storeId){
                        if($storeId == 0)  continue;

                        $catalogProductFlatTable = 'catalog_product_flat_'.$storeId;
                        $sql = "UPDATE `{$catalogProductFlatTable}` SET `discount`='".$row['discount']."' WHERE `entity_id` in('".$productId."','". $productParentId ."')";
                        //$this->getWriteAdapter()->query($sql);
                        $this->getWriteAdapter()->exec($sql);
                        $this->getWriteAdapter()->update($catalogProductFlatTable, array('on_sale' => $onSale), array('entity_id = ?' => $productId));
                        $productParentId and $this->getWriteAdapter()->update($catalogProductFlatTable, array('on_sale' => $parentOnSale), array('entity_id = ?' => $productParentId));
                    }

					$this->getWriteAdapter()->commit();
                    Mage::log($productId.'-'.$sku.'-price:'.$row['price'].'-special_price:'.$row['special_price'].'-discount:'.$row['discount'], '7', self::CONST_LOG_EXCEPTION_UPDATE_PRICES);
                    $skuExecuted[] = $sku;
                }
            } catch (Exception $e){
                Mage::log($e->getMessage(), '7', self::CONST_LOG_EXCEPTION_UPDATE_PRICES);
                $this->getWriteAdapter()->rollback();
            }
        }

        try{
            /* @var $indexProcessPrice Mage_Index_Model_Process */
            $indexProcessPrice = Mage::getModel('index/process')->load('catalog_product_price', 'indexer_code');
            $indexProcessPrice->reindexEverything();
        }catch (Exception $e){
            Mage::log($e->getMessage(), 7, self::CONST_LOG_EXCEPTION_INDEX_PRODUCT);
        }

        return $skuExecuted;
    }

    /**
     *  P5: Archive Products
     *  @param  Array  $data
     *  @return string | json
     */
    public function archiveProducts($data = array())
    {
        Mage::log(var_export($data, true), '7', self::CONST_LOG_EXCEPTION_ARCHIVE_PRODUCT);
        /* @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('catalog/product');

        $isArchivedAttribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product','is_archived');
        $isArchivedAttributeId = $isArchivedAttribute->getAttributeId();
        $isArchiveBackendTable = $isArchivedAttribute->getBackendTable();
        $skuExecuted = array();
        $writeAdapter = $this->getWriteAdapter();

        try{
            foreach ($data as $sku => $row) {
                $type = $row['type']; // simple Or Configurable
                $bool = $row['archive'];

                if ($productId = $product->getIdBySku($sku)) {
                    $writeAdapter->insertOnDuplicate($isArchiveBackendTable, array(
                        'entity_type_id'=> 4, // catalog_product
                        'attribute_id'=> $isArchivedAttributeId,
                        'store_id'=> 0,
                        'entity_id'=> $productId,
                        'value'=> $bool,
                    ));
                    Mage::log('Archive Product Sku='.$sku.' #Product Id:'.$productId.' #Is Bool='.is_bool($bool) .' #BoolBean='.$bool, '7', self::CONST_LOG_EXCEPTION_ARCHIVE_PRODUCT);

                    // Update Product Status To Disabled
                    $bool && $this->updateProductStatus($sku, 'Disabled');

                    $skuExecuted[] = $sku ;
                }
            }
        }catch (Exception $e){
            Mage::log($e->getMessage(), '7', self::CONST_LOG_EXCEPTION_ARCHIVE_PRODUCT);
        }

        try{
            /* @var $indexProcessPrice Mage_Index_Model_Process */
            $indexProcessPrice = Mage::getModel('index/process')->load('catalog_product_price', 'indexer_code');
            $indexProcessPrice->reindexEverything();
        }catch (Exception $e){
            Mage::log($e->getMessage(), 7, self::CONST_LOG_EXCEPTION_ARCHIVE_PRODUCT);
        }

        return $skuExecuted;
    }

    protected function getAttributeOptions($attribute)
    {
        if($this->_statusOptions == null) {
            $allOptionsOptions = $attribute->getSource()->getAllOptions(FALSE);
            foreach($allOptionsOptions as $option){
                $this->_statusOptions[strtolower($option['label'])] = $option['value'];
            }
        }

        return  $this->_statusOptions;
    }

    public function updateProductStatus($sku, $status)
    {
        if( !$sku ) {
            return;
        }

        $statusAttribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product','status');
        $statusAttributeId = $statusAttribute->getAttributeId();
        $statusBackendTable = $statusAttribute->getBackendTable();
        $writeAdapter = $this->getWriteAdapter();

        $statusOptions = $this->getAttributeOptions($statusAttribute);
        $status = isset($statusOptions[strtolower($status)]) ? $statusOptions[strtolower($status)] : 0;

        $productId = Mage::getSingleton('catalog/product')->getIdBySku($sku);

        try{
            $writeAdapter->insertOnDuplicate($statusBackendTable, array (
                     'entity_type_id'=> 4, // catalog_product
                     'attribute_id'=> $statusAttributeId,
                     'store_id'=> 0,
                     'entity_id'=> $productId,
                     'value'=> $status
            ));
            Mage::log('Status Product Sku='.$sku.' #Product Id='.$productId.' Status='.$status, Zend_Log::DEBUG, self::CONST_LOG_EXCEPTION_ARCHIVE_PRODUCT);
        }catch (Exception $e){
            Mage::log($e->getMessage(), Zend_Log::DEBUG, self::CONST_LOG_EXCEPTION_ARCHIVE_PRODUCT);
        }
    }

    /**
     * @return Varien_Db_Adapter_Pdo_Mysql
     */
    protected function getReadAdapter()
    {
        return Mage::getSingleton('core/resource')->getConnection('core_read');
    }

    /**
     * @return Varien_Db_Adapter_Pdo_Mysql
     */
    protected function getWriteAdapter()
    {
        return Mage::getSingleton('core/resource')->getConnection('core_write');
    }

    protected function getAttributeIdByCode($attribute_code)
    {
        $connection = $this->getReadAdapter();
        $sql = "SELECT `attribute_id`  FROM `" . $this->getTableName('eav_attribute') . "` WHERE `entity_type_id` = ? AND `attribute_code` = ?";

        $entity_type_id = $this->getEntityTypeId('catalog_product');
        return $connection->fetchOne($sql, array($entity_type_id, $attribute_code));
    }

    public function getEntityTypeId($entity_type_code)
    {
        $connection = $this->getReadAdapter();
        $sql = "SELECT `entity_type_id` FROM `" . $this->getTableName('eav_entity_type') ."` WHERE `entity_type_code` = ?";

        return $connection->fetchOne($sql, array($entity_type_code));
    }

    protected function getTableName($table_name)
    {
        return $this->getReadAdapter()->getTableName($table_name);
    }

    protected function getStoreNameByLanguage($language = '')
    {
        $map = array('zh_HK' => 'hk_cht',
             'en_US' => 'admin');

        return  isset($map[$language]) ? $map[$language] : 'admin';
    }

    public function createOfflineVip(array $data)
    {
        /* @var $offline Gri_Vip_Model_Relation_Offline */
        $offline = Mage::getModel('gri_vip/relation_offline');
        $error = $importedCardNo = array();
        foreach($data['vip'] as $_data){
            try{
                $success = true;
                $offline->unsetData();
                $offline->setData('card_no', $_data['card_no']);
                $offline->setData('created_at', Varien_Date::now());
                $offline->setData('mobilephone', $_data['mobilephone']);
                if(empty($_data['card_no']) || strlen($_data['card_no'])>32){
                    $success = false ;
                    $error[] = 'unvalid card no:'.$_data['card_no'];
                }

                if($success == true){
                    $offline->save();
                    $importedCardNo[] = $_data['card_no'];
                }
            }catch (Exception $e){
                $error[] = $e->getMessage();
            }
        }

        return  Zend_Json_Encoder::encode(array('data'=>$importedCardNo,'error'=>$error)) ;
    }

    protected function getProductParentId($productId = 0){
        if(!isset($this->_parentIds[$productId])){
            $productParentIds =  Mage::getResourceSingleton('catalog/product_indexer_eav')->getRelationsByChild($productId);
            $productParentId = isset($productParentIds[0]) && intval($productParentIds[0]) ? $productParentIds[0] : 0;
            $this->_parentIds[$productId] = $productParentId;
        }

        return $this->_parentIds[$productId] ;
    }

    /**
     * M1: Update Order Status
     * Receive Shipping status and Tracking Code.
     * @param array $data
     * @return array
     */
    public function updateOrderStatus(array $data)
    {
        Mage::log(var_export($data,true), Zend_Log::DEBUG, self::CONST_LOG_EXCEPTION_UPDATE_ORDER_STATUS);
        /* @var $order Mage_Sales_Model_Order */
        $order = Mage::getModel('sales/order');
        $locale = Mage::app()->getLocale();
        $result = array();
        foreach ($data as $update) {
            $order->reset();
            if (!isset($update['orderId'])) {
                $this->_fault('order_not_found', NULL, FALSE);
                continue;
            }
            $orderId = $update['orderId'];
            if (!isset($update['shippingTime']) || (!$shippingTime = $locale->date(
                $update['shippingTime'], $this->_datetimeFormat, NULL, FALSE
            )) || $update['shippingTime'] != $shippingTime->toString($this->_datetimeFormat)
            ) {
                $this->_fault('invalid_shipping_time', 'Invalid shipping time for order "' . $orderId . '".', FALSE);
                continue;
            }
            if (!$order->loadByIncrementId($orderId)->getId()) {
                $this->_fault('order_not_found', 'Order "' . $orderId . '" not found.', FALSE);
                continue;
            }
            if (in_array($order->getState(), $this->_orderStateNotReady)) {
                $this->_fault('order_not_ready', 'Order "' . $orderId . '" not ready to be shipped.', FALSE);
                continue;
            }
            if ($order->getState() != 'processing') {
                $this->_fault('order_has_been_shipped', 'Order "' . $orderId . '" has been shipped.', FALSE);
                continue;
            }
            if (!isset($update['shippedItems']) || !is_array($update['shippedItems'])) {
                $this->_fault('order_items_required', 'Order items not found for order "' . $orderId . '".', FALSE);
                continue;
            }
            $skip = FALSE;
            $qtyToShip = array();
            foreach ($update['shippedItems'] as $item) {
                if (!isset($item['orderItemId'])) {
                    $this->_fault('order_item_not_found', 'Order item not found for order "' . $orderId . '".', FALSE);
                    $skip = TRUE;
                    continue;
                }
                if (!$orderItem = $order->getItemById($itemId = $item['orderItemId'])) {
                    $this->_fault('order_item_not_found', 'Order item "' . $itemId . '" not found for order "' . $orderId . '".', FALSE);
                    $skip = TRUE;
                    continue;
                }
                if (!isset($item['shippedQty']) || $orderItem->getQtyToShip() < $shippedQty = $item['shippedQty']) {
                    $this->_fault('invalid_shipped_qty', 'Invalid shipped qty for order "' . $orderId . '" item "' . $itemId . '".', FALSE);
                    $skip = TRUE;
                    continue;
                }
                $qtyToShip[$itemId] = $shippedQty;
            }
            if ($skip) continue;
            /* @var $service Mage_Sales_Model_Service_Order */
            $service = Mage::getModel('sales/service_order', $order);
            $shipment = $service->prepareShipment($qtyToShip);
            if (isset($update['carrier']) && isset($update['trackingCode'])) {
                /* @var $track Mage_Sales_Model_Order_Shipment_Track */
                $track = Mage::getModel('sales/order_shipment_track')->addData(array(
                    'carrier_code' => $order->getShippingCarrier()->getCarrierCode(),
                    'title' => $update['carrier'],
                    'number' => $update['trackingCode'],
                ));
                $shipment->addTrack($track);
            }
            $shipment->setCreatedAt($shippingTime->toString('YYYY-M-d H:m:s'))->register();

            $order->setCustomerNoteNotify(FALSE)->setIsInProcess(TRUE);

            $history = $order->addStatusHistoryComment('M1: Update Order Status')
                ->setIsCustomerNotified(FALSE);
            $transactionSave = Mage::getModel('core/resource_transaction')
                ->addObject($shipment)
                ->addObject($order)
                ->save();
            $result[] =  $orderId;
        }

        return $result;
    }

    /**
     * M2: Update Rma Status
     * Receive RmaId, Status, Reason, RmaItems etc
     * @param array $data
     * @return array
     */
    public function updateRmaStatus(array $data)
    {
        /* @var $rma AW_Rma_Model_Entity */
        $rma = Mage::getModel('awrma/entity');
        /* @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('catalog/product');
        /* @var $stock Mage_CatalogInventory_Model_Stock */
        $stock = Mage::getModel('cataloginventory/stock');
        /* @var $rmaHelper AW_Rma_Helper_Data */
        $rmaHelper = Mage::helper('awrma');
        /* @var $commentHelper AW_Rma_Helper_Comments */
        $commentHelper = Mage::helper('awrma/comments');
        $locale = Mage::app()->getLocale();
        $result = array();
        foreach ($data as $update) {
            $rma->unsetData();
            if (!isset($update['rmaId'])) {
                $this->_fault('rma_not_found', NULL, FALSE);
                continue;
            }

            $rmaId = $update['rmaId'];


            if (!isset($update['updateTime']) || (!$updatedTime = $locale->date(
                $update['updateTime'], $this->_datetimeFormat, NULL, FALSE
            )) || $update['updateTime'] != $updatedTime->toString($this->_datetimeFormat)
            ) {

                $this->_fault('invalid_rma_time', 'Invalid time for Exchange Or Return"' . $rmaId . '".', FALSE);
                continue;
            }
            if (!$rma->load((int)substr($rmaId, 1))->getId()) {
                $this->_fault('rma_not_found', 'Rma "' . $rmaId . '" not found.', FALSE);
                continue;
            }
            if ($rma->getStatus() == 6) {
                $this->_fault('rma_processed', 'Rma "' . $rmaId . '" has been processed.', FALSE);
                continue;
            }
            if (!isset($update['rmaItems']) || !is_array($update['rmaItems'])) {
                $this->_fault('rma_items_required', 'Rma items not found for Return Or Exchange "' . $rmaId . '".', FALSE);
                continue;
            }
            $skip = FALSE;
            $rmaItems = $rma->load(null)->getOrderItems();
            foreach ($update['rmaItems'] as $item) {
                if (!isset($item['rmaItemId'])) {
                    $this->_fault('rma_item_not_found', 'Rma item not found for Exchange Or Return "' . $rmaId . '".', FALSE);
                    $skip = TRUE;
                    continue;
                }
                if (!isset($rmaItems[$itemId = substr($item['rmaItemId'], 11)])) {
                    $this->_fault('rma_item_not_found', 'Rma item "' . $itemId . '" not found for Exchange Or Return "' . $rmaId . '".', FALSE);
                    $skip = TRUE;
                    continue;
                }

                if (!isset($item['returnedQty']) || $rmaItems[$itemId] != $item['returnedQty']) {


                    $this->_fault('invalid_return_qty', $rmaItems[$itemId].'==='.$item['returnedQty'].'Invalid rma qty for Exchange Or Return"' . $rmaId . '" item "' . $itemId . '".', FALSE);
                    $skip = TRUE;
                    continue;
                }
            }

            if ($skip) continue;
            $trackingCode = array();

            $statuses = array('received' => 10,
                               'rejected' => 9);
            $rma->setStatus($statuses[strtolower($update['status'])])
                ->save();

            $rma->getOrder()->addStatusHistoryComment('M2: Update Rma Status')
                ->setIsCustomerNotified(FALSE);

            $rma->getOrder()->save();
            $commentHelper::postComment($rma->getId(), $update['reason'], array (
                'created_at' => $updatedTime->toString('YYYY-M-d H:m:s'),
                'owner' => AW_Rma_Model_Source_Owner::SYSTEM,
            ), FALSE);

            $result[] = $rmaId;
        }

        return $result;
    }
}
