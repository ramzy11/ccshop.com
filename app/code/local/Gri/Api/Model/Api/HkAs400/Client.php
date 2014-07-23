<?php
/**
 * @method string getAppKey() Get the APP Key for HK AS400 API invocation
 * @method string getAppSecret() Get the APP Secret for HK AS400 API invocation
 * @method string getAppServer() Get the APP Server URL for HK AS400 API invocation
 * @method boolean getEnabled() Get the HKAS400 API status
 * @method Gri_Api_Model_Api_HkAs400_Client setAppKey(string $appKey) Set the APP Key for HK AS400 API invocation
 * @method Gri_Api_Model_Api_HkAs400_Client setAppSecret(string $appSecret) Set the APP Secret for HK AS400 API invocation
 * @method Gri_Api_Model_Api_HkAs400_Client setAppServer(string $appServer) Set the APP Server URL for HK AS400 API invocation
 */
class Gri_Api_Model_Api_HkAs400_Client extends Varien_Object
{
    const CONFIG_PATH_ENABLED = 'gri_api/hkAs400_client/active';
    const CONFIG_PATH_APP_KEY = 'gri_api/hkAs400_client/app_key';
    const CONFIG_PATH_APP_SECRET = 'gri_api/hkAs400_client/app_secret';
    const CONFIG_PATH_APP_SERVER = 'gri_api/hkAs400_client/app_server';

    const METHOD_NOTIFY_NEW_ORDER = 'newOrders';
    const METHOD_NOTIFY_ERROR = 'ProductsResponse';
    const METHOD_CANCEL_ORDER = 'cancelOrders';
    const METHOD_NEW_RMAS = 'newRmas';
    const METHOD_CREATE_REFUND = 'createRefund';

    protected $_logFile = 'api.hkAs400.client.log';
    protected $_logSuccessFile = 'api.hkAs400.client.success.log';
    protected $_logSuccessErrorsNum = 'api.hkAs400.num.log';
    protected $_logError = 'api.hkAs400.error.log';
    protected $_datetimeFormat = 'yyyyMMddHHmmss';
    protected $_orderFieldsMapping = array (
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

    protected $_orderItemFieldsMapping = array (
        'orderItemId' => 'item_id',
        'sku' => 'sku',
        'productName' => 'name',
        'price' => 'original_price',
        'basePrice' => 'base_price',
        'weight' => 'weight',
        'qty' => 'qty_ordered',
        'subtotal' => 'row_total',
        'baseSubtotal' => 'base_row_total',
        'discountAmount' => 'discount_amount',
        'baseDiscountAmount'=> 'base_discount_amount',
        'rowWeight' => 'row_weight',
        'rowTotal' => 'final_row_total',
        'baseRowTotal' => 'base_row_total'
    );

    protected $_types = null;

    protected function _construct()
    {
        $appServer = Mage::getStoreConfig(self::CONFIG_PATH_APP_SERVER);
        $appServer or $appServer = 'http://localhost/test/javascript/sample.json.php/';
        substr($appServer, -1) == '/' or $appServer .= '/';
        $this->setEnabled(Mage::getStoreConfig(self::CONFIG_PATH_ENABLED))
            ->setAppKey(Mage::getStoreConfig(self::CONFIG_PATH_APP_KEY))
            ->setAppSecret(Mage::getStoreConfig(self::CONFIG_PATH_APP_SECRET))
            ->setAppServer($appServer);
        parent::_construct();
    }

    protected function _invoke($method, $transactionId, $data)
    {
        $result = $body = $error = NULL;
        $client = $this->getClient()->resetParameters();
        $client->setConfig(array('timeout'=> '30'));
        $request = array(
            'method' => 'call',
            'params' => array(
                $method,
                $this->getAppKey(),
                $this->getAppSecret(),
                $transactionId,
                $data,
            )
        );
        $request = Zend_Json_Encoder::encode($request);
        $client->setUri($this->getAppServer())
            ->setMethod(Zend_Http_Client::POST)
            ->setRawData($request)
        ;


        try {
            $result = Zend_Json::decode($body = trim($client->request()->getBody()));
            isset($result['errors']) and $error = $result['errors'];
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
        if ($error) {
            Mage::log('Error while calling "' . $method . '": ' . $error, Zend_Log::ERR, $this->_logFile);
            Mage::log('Request body: ' . var_export($request, TRUE), Zend_Log::ERR, $this->_logFile);
            Mage::log('Response body: ' . var_export($body, TRUE), Zend_Log::ERR, $this->_logFile);
        }
        else {
            Mage::log('Success: "' . $method, Zend_Log::INFO, $this->_logSuccessFile);
            Mage::log('Request body: ' . var_export($request, TRUE), Zend_Log::INFO, $this->_logSuccessFile);
            Mage::log('Response body: ' . var_export($body, TRUE), Zend_Log::INFO, $this->_logSuccessFile);
        }
        return array(
            'json' => $result,
            'raw' => $body,
            'error' => $error,
        );
    }

    /**
     * @return Zend_Http_Client
     */
    public function getClient()
    {
        if (!$this->getData('client') instanceof Zend_Http_Client) {
            $this->setData('client', new Zend_Http_Client());
        }
        return $this->getData('client');
    }

    public function getFormattedTime($time = NULL, $useTimezone = TRUE)
    {
        return Mage::app()->getLocale()->date($time, Zend_Date::ISO_8601, NULL, $useTimezone)->toString($this->_datetimeFormat);
    }

    protected function _mapFields(Varien_Object $object, array $fieldsMappingTable)
    {
        $result = array();
        foreach ($fieldsMappingTable as $k => $v) {
            $result[$k] = $object->getData($v);
        }
        return $result;
    }

    public function methodNotifyErrorInfo()
    {
        /* @var $readConnection Varien_Db_Adapter_Pdo_Mysql */
        $readConnection = Mage::getSingleton('core/resource')->getConnection('core_read');
        /* @var $writeConnection Varien_Db_Adapter_Pdo_Mysql */
        $writeConnection = Mage::getSingleton('core/resource')->getConnection('core_write');

        $maxCount = 10000;
        $table = $readConnection->getTableName('gri_api_product');
        $sql = "SELECT `id` , `transaction_id`, `error_info`, `json` FROM `{$table}` WHERE `is_sent`= 0 AND `status` = 1 ORDER BY id ASC LIMIT {$maxCount};";
        $updateSql = "UPDATE `{$table}` SET `is_sent`= 1, `sent_at` = '%s' WHERE `id` IN (?);";
        $start_time = $_SERVER["REQUEST_TIME"];
        try {
            $notifyInfo = $readConnection->fetchAll($sql);
            $notifyInfo = $this->getGroupResponse($notifyInfo, $maxCount);

            foreach ($notifyInfo as $transactionId => $request) {
                // Exit if cron time exceeded
                if (time() - $start_time > 60) {
                    break;
                }
                // check if invoke it
                if (!$this->checkAllProcessed($transactionId)) {
                    continue;
                }

                try {
                    $ids = $request['ids'];
                    $request = array('success', $request['success'], 'errors', $request['errors']);
                    $result = $this->_invoke(self::METHOD_NOTIFY_ERROR, $transactionId, $request);
                    if ($result) {
                        if ($result['error']) {
                            // TODO Handle errors
                            Mage::log(var_export(array($result['error']), TRUE), Zend_Log::ERR, $this->_logError);
                        } else {
                            $sentAt = Varien_Date::now();
                            $writeConnection->query($writeConnection->quoteInto(sprintf($updateSql, $sentAt), $ids));
                        }
                    }
                } catch (Exception $e) {
                    Mage::log($e->getMessage(), NULL, $this->_logFile);
                }
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }
        return 'ok';
    }

    protected function getGroupResponse($data = array(), $maxCount = 1000)
    {
        $result = array();
        $skipLastTransaction = count($data) == $maxCount;
        $lastTransaction = array();
        $skipLastTransaction and $lastTransaction = end($data);

        foreach ($data as $_data) {
            $transactionId = $_data['transaction_id'];
            if ($skipLastTransaction && $transactionId == $lastTransaction['transaction_id']) continue;
            isset($result[$transactionId]['success']) or $result[$transactionId]['success'] = array();
            isset($result[$transactionId]['errors']) or $result[$transactionId]['errors'] = array();
            isset($result[$transactionId]['ids']) or $result[$transactionId]['ids'] = array();
            $result[$transactionId]['ids'][] = $_data['id'];
            $json = Zend_Json_Decoder::decode($_data['json']);
            if ( empty($_data['error_info']) ) {
                $result[$transactionId]['success'][] = array('sku' => $json['sku']);
            } else {
                $result[$transactionId]['errors'][] = array('sku' => $json['sku'], 'message' => $_data['error_info']);
            }
        }

        return $result;
    }

    protected function checkAllProcessed($transactionId)
    {
        /* @var $readConnection Varien_Db_Adapter_Pdo_Mysql*/
        $readConnection = Mage::getSingleton('core/resource')->getConnection('core_read');

        // Get unprocessed count
        $sql = "SELECT count(*) AS `num` FROM `{$readConnection->getTableName('gri_api_product')}` WHERE `transaction_id` = ? AND status=0;" ;
        $result = $readConnection->fetchOne($sql, array($transactionId));

        return !$result;
    }

    /**
     * A1 - New Orders
     */
    public function methodNewOrders()
    {
        if (!$this->getEnabled()) return;

        /* @var $orders Mage_Sales_Model_Resource_Order_Collection */
        $orders = Mage::getModel('sales/order')->getCollection();
        $this->getGriApiHeler()->addRetryCountFilter($orders);
        $orders->addAttributeToSearchFilter('status', 'processing');
        /* @var $transactionSave Mage_Core_Model_Resource_Transaction */
        $transactionSave = Mage::getModel('core/resource_transaction');

        /* @var $order Mage_Sales_Model_Order */
        foreach ($orders as $order) {
            $time = $order->getPaidAt();
            $time or $time = $order->getCreatedAt();
            /* @var $orderPayment Mage_Sales_Model_Entity_Order_Payment */
            $orderPayment = $order->getPayment();
            /* @var $invoice Mage_Sales_Model_Entity_Order_Invoice */
            $invoice =$order->getInvoiceCollection()->getFirstItem();
            /* @var $orderAddress Mage_Sales_Model_Order_Address */
            $orderAddress = $order->getShippingAddress();
            /* @var $creditMemo Mage_Sales_Model_Order_Creditmemo */
            $creditMemo = Mage::getSingleton('sales/order_creditmemo');
            $items = array();
            // Process order items
            /* @var $item Mage_Sales_Model_Order_Item */
            foreach ($order->getItemsCollection() as $item) {
                if ($item->getParentItemId()) continue;
                $item->setFinalRowTotal($item->getRowTotal() - $item->getDiscountAmount());
                $items[] = $this->_mapFields($item, $this->_orderItemFieldsMapping);
            }

            $parentRmaId = $order->getFromRmaId();
            $creditMemo = $creditMemo->unsetData()->load($order->getFromCreditmemoId());
            $parentOrderId = Mage::getSingleton('sales/order')->unsetData()->load($creditMemo->getOrderId())->getIncrementId();
            $orderType = $order->getOrderType() ? $order->getOrderType() : 'sale';

            $address = $orderAddress->getStreet();
            $request = array (
                array (
                'orderId' => $order->getIncrementId(),
                'payTime' => $this->getFormattedTime($time),
                'status' => 'processing',
		        "createTime" => $this->getFormattedTime($order->getCreatedAt()),  // "yyyyMMddhhmmss",
		        "type" => $orderType, /* One of "sale", "exchange" or "amend" */
		        "parentOrderId" => strval($parentOrderId), /* Required for type "exchange" and "amend" */
		        "parentRmaId" => strval($parentRmaId), /* Required for type "exchange" */
                "customerId" => strval($order->getCustomerId()),
                "customerName"=> $order->getCustomerLastname().' '.$order->getCustomerFirstname(),
                "customerEmail" => $order->getCustomerEmail(),
                "customerGroup" => Mage::helper('gri_customer')->getGroupNameById($order->getCustomerGroupId()),
                "currency" => $order->getOrderCurrencyCode(), /* HKD or USD, assumed rate 7.7 */
                "subtotal" => $order->getSubtotal(), /* Product summary */
                "baseSubtotal" => $order->getBaseSubtotal(), /* base currency is HKD */
                "discountAmount" => $order->getDiscountAmount(), /* Shopping cart price rules, e.g. coupons */
                "baseDiscountAmount" => $order->getBaseDiscountAmount(),
                "shippingAmount" => $order->getShippingAmount(),
                "baseShippingAmount" => $order->getBaseShippingAmount(),
                "grandTotal" => $order->getGrandTotal(), /* To be paid */
                "baseGrandTotal" => $order->getBaseGrandTotal(),
                "totalPaid" => $order->getTotalPaid() , /* Actually paid */
                "baseTotalPaid" => $order->getBaseTotalPaid(),
                "itemCounts" => $order->getItemsCollection()->getSize() , /* Total rows of items */
                "totalQty" => $order->getTotalQtyOrdered(), /* Total QTY of products */
                "weight" => $order->getWeight(), /* Total weight of products */
                "paymentMethod" => $orderPayment->getMethod(),
                "paymentTransactionId" => $invoice->getTransactionId(),
                "recipient" => $orderAddress->getFirstname().' '.$orderAddress->getLastname(),
                "shippingMethod" => $order->getShippingMethod(),
                "country" => $orderAddress->getCountryId(),
                "state" => $orderAddress->getRegion(),
                "city" => $orderAddress->getCity(),
                "address" => isset($address[0]) ? $address[0] : '',
                "postcode" => $orderAddress->getPostcode(),
                "telephone" => $orderAddress->getTelephone(),
                "mobile" => $orderAddress->getTelephone(),
                "remarks" => $order->getRemarks(),
                "orderItems" => $items
                )
            );

            $result = $this->_invoke(self::METHOD_NOTIFY_NEW_ORDER, 0, $request);
            if ($result['error']) {
                // TODO Handle errors
                $result['raw'] and $this->getGriApiHeler()->increaseRetryCount($order);
            }
            else {
                $this->getGriApiHeler()->resetRetryCount($order, FALSE);
                $order->addStatusHistoryComment('A1: New Orders', 'notified')
                    ->setIsCustomerNotified(FALSE);
                $transactionSave->addObject($order);
            }
        }
        $transactionSave->save();
    }


    /**
     *  A2: Cancel Orders
     */
    public function methodCancelOrders()
    {
        if (!$this->getEnabled()) return;
        /* @var $creditmemos Mage_Sales_Model_Resource_Order_Creditmemo_Collection */
        $creditmemos = Mage::getModel('sales/order_creditmemo')->getCollection();
        $this->getGriApiHeler()->addRetryCountFilter($creditmemos);
        $creditmemos->getFiltered(array(
            'state' => Gri_Sales_Model_Order_Creditmemo::STATE_ORDER_CANCELED
        ));
        /* @var $transactionSave Mage_Core_Model_Resource_Transaction */
        $transactionSave = Mage::getModel('core/resource_transaction');
        /* @var $creditmemo Gri_Sales_Model_Order_Creditmemo */
        foreach ($creditmemos as $creditmemo) {
            $order = $creditmemo->getOrder();
            $time = $creditmemo->getCreatedAt();
            $comments = array();
            /* @var $comment Mage_Sales_Model_Order_Creditmemo_Comment */
            foreach ($creditmemo->getCommentsCollection() as $comment) {
                if ($comment->getType()) continue;
                $comments[] = $comment->getComment();
            }

            $request = array(array (
                               'orderId' => $order->getIncrementId(),
                               'reason' => implode(";\r\n", $comments),
                           ));

            $result = $this->_invoke(self::METHOD_CANCEL_ORDER, 0, $request);
            if ($result['error']) {
                // TODO Handle errors
                $result['raw'] and $this->getGriApiHeler()->increaseRetryCount($creditmemo);
            }
            else {
                $this->getGriApiHeler()->resetRetryCount($creditmemo, FALSE);
                $raw = Zend_Json_Decoder::decode($result['raw']);
                if(isset($raw['data'][0]) && $raw['data'][0] == $order->getIncrementId()){
                    $creditmemo->setState($creditmemo::STATE_NOTIFIED);
                    $order->addStatusHistoryComment('A2: Cancel Order')
                        ->setIsCustomerNotified(FALSE);
                    $transactionSave->addObject($creditmemo)->addObject($order);
                }
            }
        }

        $transactionSave->save();
    }


   /**
    *  A3: New RMAs
    */
   public function methodNewRmas()
   {
       if (!$this->getEnabled()) return;
       /* @var $rmas AW_Rma_Model_Mysql4_Entity_Collection */
       $rmas = Mage::getModel('awrma/entity')->getCollection();
       $this->getGriApiHeler()->addRetryCountFilter($rmas);
       // Status 2: Approved
       $rmas->setStatusFilter(2);
       /* @var $transactionSave Mage_Core_Model_Resource_Transaction */
       $transactionSave = Mage::getModel('core/resource_transaction');
       /* @var $commentHelper AW_Rma_Helper_Comments */
       $commentHelper = Mage::helper('awrma/comments');
       $requestTypes = $this->getRequestTypes();
       /* @var $rma AW_Rma_Model_Entity */
       foreach ($rmas as $rma) {
           $order = $rma->getOrder();
           $time = $rma->getUpdatedAt();

           $entityCommentsCollection = Mage::getSingleton('awrma/Entitycomments')->getCollection();
           /* @var $comment AW_Rma_Model_Entitycomments */
           foreach ($entityCommentsCollection as $comment) {
               $comments[] = $comment->getText();
           }

           $rmaItems = $this->getAllRmaItems($rma);
           $request = array(
               'rmaId' => $rma->getTextId(),
		       "type" => $requestTypes[$rma->getRequestType()], // "exchange", /* "return" or "exchange" */
		       "orderId"=> $order->getIncrementId(),
               'createTime' => $this->getFormattedTime($time),
		       "reason" => implode(";\r\n", $comments),
		       "rmaItems" => $rmaItems,

           );
           $result = $this->_invoke(self::METHOD_NEW_RMAS, 0, $request);
           if ($result['error']) {
               // TODO Handle errors
               $result['raw'] and $this->getGriApiHeler()->increaseRetryCount($rma);
           }
           else {
               $this->getGriApiHeler()->resetRetryCount($rma, FALSE);
               // Status 8:  HKAas400 Notified
               $rma->setStatus(8);
               $order->addStatusHistoryComment('A3: New RMAS')
                   ->setIsCustomerNotified(FALSE);
               $transactionSave->addObject($rma)->addObject($order);
               $commentHelper::postComment($rma->getId(), $commentHelper->__('RMA request sent to HkAs400.'), array(
                   'owner' => AW_Rma_Model_Source_Owner::SYSTEM,
               ), FALSE);
           }
       }

       $transactionSave->save();
   }

    protected function getAllRmaItems(AW_Rma_Model_Entity $rma)
    {
        $orderItems = $rma->load(null)->getOrderItems();
        $exchangeItems = $rma->load(null)->getExchangeItems();

        $allRmaItems = array();

        /* @var $orderItem Mage_Sales_Model_Order_Item */
        $orderItem = Mage::getSingleton('sales/order_item');
        if( $orderItems && count($orderItems)) {
            foreach($orderItems as $orderItemId => $qty) {
                $orderItem = $orderItem->unsetData()->load( $orderItemId );
                $exchangeSku = '';
                // 1: Exchange
                if($rma->getRequestType() == 1 && isset($exchangeItems[$orderItemId]) && !empty($exchangeItems[$orderItemId])){
                    $exchangeSku = $exchangeItems[$orderItemId];
                }
                $rmaItem = array (
                    'rmaItemId' => $rma->getTextId().$orderItemId,
                    'orderItemId' => $orderItemId,
                    'sku' => $orderItem->getSku(),
                    'productName' => $orderItem->getName(),
                    'qty' => $qty,
                    'exchangeSku' => $exchangeSku
                );

                $allRmaItems[] = $rmaItem;
            }
        }

        return $allRmaItems;
    }

    protected function getRequestTypeNameById( $typeId ){
        return Mage::getModel('awrma/entitytypes')->load($typeId)->getName();
    }

    /**
     * @return Gri_Api_Helper_Data
     */
    public function getGriApiHeler()
    {
        return Mage::helper('gri_api');
    }

    protected function getRequestTypes()
    {
        if(is_null($this->_types)) {
            /* @var $collection AW_Rma_Model_Mysql4_Entitytypes_Collection*/
            $collection =  Mage::getModel('awrma/entitytypes')->getCollection();
            $types = array();
            foreach($collection->toOptionArray() as $option ) {
                $key = $option['value'];
                $types[$key] = strtolower($option['label']);
            }
            $this->_types = $types ;
        }

        return $this->_types;
    }
}
