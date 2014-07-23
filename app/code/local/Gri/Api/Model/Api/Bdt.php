<?php

/**
 * BDT API Server
 */
class Gri_Api_Model_Api_Bdt extends Mage_Api_Model_Resource_Abstract
{
    protected $_datetimeFormat = 'YYYYMMddHHmmss';
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

    public function __construct()
    {
        $this->_getAdapter()->setFaults($this->_getConfig()->getFaults());
    }

    protected function _fault($code, $customMessage = NULL, $terminate = TRUE)
    {
        if ($terminate) parent::_fault($code, $customMessage);
        else {
            $this->_getAdapter()->addError($code, $customMessage);
        }
    }

    /**
     * @return Gri_Api_Model_Api_Bdt_Adapter_Rest
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
     * M1: Get Order Info
     * @param array $data
     * @return array
     */
    public function getOrderInfo(array $data)
    {
        if (!isset($data['orderId'])) $this->_fault('order_not_found');
        $orderId = $data['orderId'];
        /* @var $order Mage_Sales_Model_Order */
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
        $order->getId() && $order->getState() != 'pending'
            or $this->_fault('order_not_found', 'Order "' . $orderId . '" not found.');
        $locale = Mage::app()->getLocale();
        /* @var $customerGroup Mage_Customer_Model_Group */
        $customerGroup = Mage::getModel('customer/group')->load($order->getCustomerGroupId());
        /* @var $invoice Mage_Sales_Model_Order_Invoice */
        $invoice = $order->getInvoiceCollection()->getFirstItem();
        $address = $order->getShippingAddress();
        $items = array();

        // Process order items
        /* @var $item Mage_Sales_Model_Order_Item */
        foreach ($order->getItemsCollection() as $item) {
            if ($item->getParentItemId()) continue;
//            $product = $item->getProduct();
            $item->setFinalRowTotal($item->getRowTotal() - $item->getDiscountAmount());
            if (($productOptions = $item->getProductOptions()) && isset($productOptions['attributes_info'])) {
                $options = array();
                foreach ($productOptions['attributes_info'] as $o) {
                    $options[$o['label']] = $o['value'];
                }
                $item->setOptions($options);
            }
            $items[] = $this->_mapFields($item, $this->_orderItemFieldsMapping);
        }

        // Convert / fetch order fields
        $object = new Varien_Object($order->getData());
        $object->setCustomerName($order->getCustomerName())
            ->setCreateTime($locale->date($order->getCreatedAt(), Zend_Date::ISO_8601)->toString($this->_datetimeFormat))
            ->setCustomerGroup($customerGroup->getCustomerGroupCode())
            ->setPaymentMethod($order->getPayment()->getMethod())
            ->setPaymentTransactionId($invoice->getTransactionId())
            ->setPayTime($locale->date($invoice->getCreatedAt(), Zend_Date::ISO_8601)->toString($this->_datetimeFormat))
            ->setRecipient($address->getName())
            ->setShippingMethod($order->getShippingCarrier()->getConfigData('title'))
            ->setData('shipping_address', $address->format('api'))
            ->setPostcode($address->getPostcode())
            ->setTelephone($address->getTelephone())
            ->setMobile($address->getMobile())
            ->setOrderItems($items)
            ->setFapiaoType($order->getFapiao() ? 1 : 0)
            ->setDiscountAmount(abs($order->getDiscountAmount()))
        ;
        $result = $this->_mapFields($object, $this->_orderFieldsMapping);
        $history = $order->addStatusHistoryComment('M1: Get Order Info')
            ->setIsCustomerNotified(FALSE);
        $order->save();
        return $result;
    }

    /**
     * M4: Update Exchange Status
     * @param array $data
     * @return array
     */
    public function updateExchangeStatus(array $data)
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
            if (!isset($update['exchangeId'])) {
                $this->_fault('exchange_not_found', NULL, FALSE);
                continue;
            }
            $exchangeId = $update['exchangeId'];
            if (substr($exchangeId, 0, 1) != 'E') {
                $this->_fault('exchange_not_found', 'Exchange "' . $exchangeId . '" not found.', FALSE);
                continue;
            }
            if (!isset($update['exchangeTime']) || (!$exchangeTime = $locale->date(
                $update['exchangeTime'], $this->_datetimeFormat, NULL, FALSE
            )) || $update['exchangeTime'] != $exchangeTime->toString($this->_datetimeFormat)
            ) {
                $this->_fault('invalid_exchange_time', 'Invalid exchange time for Exchange "' . $exchangeId . '".', FALSE);
                continue;
            }
            if (!$rma->load((int)substr($exchangeId, 1))->getId()) {
                $this->_fault('exchange_not_found', 'Exchange "' . $exchangeId . '" not found.', FALSE);
                continue;
            }
            if ($rma->getStatus() == 6) {
                $this->_fault('exchange_processed', 'Exchange "' . $exchangeId . '" has been processed.', FALSE);
                continue;
            }
            if (!isset($update['exchangedItems']) || !is_array($update['exchangedItems'])) {
                $this->_fault('exchange_items_required', 'Exchange items not found for Exchange "' . $exchangeId . '".', FALSE);
                continue;
            }
            $skip = FALSE;
            $exchangeItems = $rma->getOrderItems();
            foreach ($update['exchangedItems'] as $item) {
                if (!isset($item['exchangeItemId'])) {
                    $this->_fault('exchange_item_not_found', 'Exchange item not found for Exchange "' . $exchangeId . '".', FALSE);
                    $skip = TRUE;
                    continue;
                }
                if (!isset($exchangeItems[$itemId = substr($item['exchangeItemId'], 11)])) {
                    $this->_fault('exchange_item_not_found', 'Exchange item "' . $itemId . '" not found for Exchange "' . $exchangeId . '".', FALSE);
                    $skip = TRUE;
                    continue;
                }
                if (!isset($item['newSku'])) {
                    $this->_fault('sku_not_found', 'New SKU not found for Exchange "' . $exchangeId . '".', FALSE);
                    $skip = TRUE;
                    continue;
                }
                if (!$productId = $product->getIdBySku($item['newSku'])) {
                    $this->_fault('sku_not_found', 'New SKU "' . $item['newSku'] . '" not found for Exchange "' . $exchangeId . '".', FALSE);
                    $skip = TRUE;
                    continue;
                }
                if (!isset($item['exchangedQty']) || $exchangeItems[$itemId] != $item['exchangedQty']) {
                    $this->_fault('invalid_exchange_qty', 'Invalid exchange qty for Exchange "' . $exchangeId . '" item "' . $itemId . '".', FALSE);
                    $skip = TRUE;
                    continue;
                }
                $stock->registerItemSale($product->setProductId($productId)->setQtyOrdered($item['exchangedQty']));
            }
            if ($skip) continue;
            $trackingCode = array();
            isset($update['carrier']) and $trackingCode[] = $update['carrier'];
            isset($update['trackingCode']) and $trackingCode[] = $update['trackingCode'];
            $rma->setTrackingCode(implode(' ', $trackingCode))->setStatus(6)->save();
            $rma->getOrder()->addStatusHistoryComment('M4: Update Exchange Status', 'exchange_complete')
                ->setIsCustomerNotified(FALSE);
            $rma->getOrder()->save();
            $commentHelper::postComment($rma->getId(), $rmaHelper->__('RMA items exchanged.'), array(
                'created_at' => $exchangeTime->toString('YYYY-M-d H:m:s'),
                'owner' => AW_Rma_Model_Source_Owner::SYSTEM,
            ), FALSE);
            $result[] = array('rmaId' => $exchangeId, 'status' => 'returnComplete');
        }

        return $result;
    }

    /**
     * M5: Update Inventory
     * @param array $data
     * @return string
     */
    public function updateInventory(array $data)
    {
        if (isset($data['stock']) && is_array($data['stock'])) {
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

            $write->beginTransaction();

            // Reset inventory
            $reset = !isset($data['reset']) || $data['reset'];
            count($data['stock']) == 1 and $reset = FALSE;
            if ($reset) {
                $sql = "UPDATE `{$stockItemTable}` i JOIN `{$productTable}` p ON p.entity_id = i.product_id AND p.type_id = 'simple' SET i.qty=0, i.is_in_stock=0";
                $write->query($sql);

                $sql = "UPDATE `{$stockStatusTable}` i JOIN `{$productTable}` p ON p.entity_id = i.product_id AND p.type_id = 'simple' SET i.qty=0, i.stock_status=0 WHERE i.website_id=1";
                $write->query($sql);

                $sql = "UPDATE `{$stockStatusIdxTable}` i JOIN `{$productTable}` p ON p.entity_id = i.product_id AND p.type_id = 'simple' SET i.qty=0, i.stock_status=0 WHERE i.website_id=1";
                $write->query($sql);
            }

            foreach ($data['stock'] as $sku => $qty) {
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
                        Mage::log($row['product_id'] . '-' . $sku . '-' . $qty, NULL, 'stock.log');
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
                    }
                }
            }

            $write->commit();
            /* @var $indexProcessPrice Mage_Index_Model_Process */
            $indexProcessPrice = Mage::getModel('index/process')->load('catalog_product_price', 'indexer_code');
            $indexProcessPrice->reindexEverything();
        }
        return 'ok';
    }

    /**
     * M2: Update Order Status
     * @param array $data
     * @return array
     */
    public function updateOrderStatus(array $data)
    {
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
            $history = $order->addStatusHistoryComment('M2: Update Order Status')
                ->setIsCustomerNotified(FALSE);
            $transactionSave = Mage::getModel('core/resource_transaction')
                ->addObject($shipment)
                ->addObject($order)
                ->save();
            $result[] = array('orderId' => $orderId, 'status' => $order->getStatus());
        }

        return $result;
    }

    /**
     * M3: Update Return Status
     * @param array $data
     * @return array
     */
    public function updateReturnStatus(array $data)
    {
        /* @var $rma AW_Rma_Model_Entity */
        $rma = Mage::getModel('awrma/entity');
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
            if (substr($rmaId, 0, 1) != 'R') {
                $this->_fault('rma_not_found', 'RMA "' . $rmaId . '" not found.', FALSE);
                continue;
            }
            if (!isset($update['rmaTime']) || (!$rmaTime = $locale->date(
                $update['rmaTime'], $this->_datetimeFormat, NULL, FALSE
            )) || $update['rmaTime'] != $rmaTime->toString($this->_datetimeFormat)
            ) {
                $this->_fault('invalid_rma_time', 'Invalid RMA time for RMA "' . $rmaId . '".', FALSE);
                continue;
            }
            if (!$rma->load((int)substr($rmaId, 1))->getId()) {
                $this->_fault('rma_not_found', 'RMA "' . $rmaId . '" not found.', FALSE);
                continue;
            }
            if ($rma->getStatus() == 5) {
                $this->_fault('rma_closed', 'RMA "' . $rmaId . '" has been closed.', FALSE);
                continue;
            }
            if (!isset($update['rmaItems']) || !is_array($update['rmaItems'])) {
                $this->_fault('rma_items_required', 'RMA items not found for RMA "' . $rmaId . '".', FALSE);
                continue;
            }
            $skip = FALSE;
            // $rmaItems is an array stored in field order_items
            $rmaItems = $rma->getOrderItems();
            foreach ($update['rmaItems'] as $item) {
                if (!isset($item['rmaItemId'])) {
                    $this->_fault('rma_item_not_found', 'RMA item not found for RMA "' . $rmaId . '".', FALSE);
                    $skip = TRUE;
                    continue;
                }
                if (!isset($rmaItems[$itemId = substr($item['rmaItemId'], 11)])) {
                    $this->_fault('rma_item_not_found', 'RMA item "' . $itemId . '" not found for RMA "' . $rmaId . '".', FALSE);
                    $skip = TRUE;
                    continue;
                }
                if (!isset($item['returnedQty']) || $rmaItems[$itemId] != $item['returnedQty']) {
                    $this->_fault('invalid_returned_qty', 'Invalid returned qty for RMA "' . $rmaId . '" item "' . $itemId . '".', FALSE);
                    $skip = TRUE;
                    continue;
                }
            }
            if ($skip) continue;
            $rma->setOrderItems($rmaItems)->setStatus(11)->save();
            $rma->getOrder()->addStatusHistoryComment('M3: Update Return Status', 'return_received')
                ->setIsCustomerNotified(FALSE);
            $rma->getOrder()->save();
            $commentHelper::postComment($rma->getId(), $rmaHelper->__('RMA items received.'), array(
                'created_at' => $rmaTime->toString('YYYY-M-d H:m:s'),
                'owner' => AW_Rma_Model_Source_Owner::SYSTEM,
            ), FALSE);
            $result[] = array('rmaId' => $rmaId, 'status' => 'returnComplete');
        }

        return $result;
    }
}
