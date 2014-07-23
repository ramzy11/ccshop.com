<?php

/**
 * @method string getAppKey() Get the APP Key for BDT API invocation
 * @method string getAppSecret() Get the APP Secret for BDT API invocation
 * @method string getAppServer() Get the APP Server URL for BDT API invocation
 * @method boolean getEnabled() Get the BDT API status
 * @method Gri_Api_Model_Api_Bdt_Client setAppKey(string $appKey) Set the APP Key for BDT API invocation
 * @method Gri_Api_Model_Api_Bdt_Client setAppSecret(string $appSecret) Set the APP Secret for BDT API invocation
 * @method Gri_Api_Model_Api_Bdt_Client setAppServer(string $appServer) Set the APP Server URL for BDT API invocation
 * @method Gri_Api_Model_Api_Bdt_Client setEnabled(boolean $enabled) Set the BDT API status
 */
class Gri_Api_Model_Api_Bdt_Client extends Varien_Object
{
    const CONFIG_PATH_APP_KEY = 'gri_api/bdt_client/app_key';
    const CONFIG_PATH_APP_SECRET = 'gri_api/bdt_client/app_secret';
    const CONFIG_PATH_APP_SERVER = 'gri_api/bdt_client/app_server';
    const CONFIG_PATH_ENABLED = 'gri_api/bdt_client/active';

    const METHOD_CANCEL_EXCHANGE = 'cancelExchange';
    const METHOD_CANCEL_ORDER = 'cancelOrder';
    const METHOD_CANCEL_REFUND = 'cancelRefund';
    const METHOD_CANCEL_RETURN = 'cancelReturn';
    const METHOD_CLOSE_REFUND = 'closeRefund';
    const METHOD_CREATE_EXCHANGE = 'createExchange';
    const METHOD_CREATE_REFUND = 'createRefund';
    const METHOD_CREATE_RETURN = 'createReturn';
    const METHOD_NOTIFY_NEW_ORDER = 'notifyNewOrder';
    const METHOD_UPDATE_REFUND = 'updateRefund';

    protected $_logFile = 'api.bdt.client.log';
    protected $_logSuccessFile = 'api.bdt.client.success.log';
    protected $_datetimeFormat = 'YYYYMMddHHmmss';

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

    protected function _invoke($method, $request)
    {
        $result = $body = $error = NULL;
        $client = $this->getClient()->resetParameters();
        $request = array(
            'appKey' => $this->getAppKey(),
            'timestamp' => $time = $this->getFormattedTime(),
            'sign' => $this->getSignature($time),
            'data' => Zend_Json::encode($request),
        );
        $client->setUri($this->getAppServer() . $method)
            ->setMethod(Zend_Http_Client::POST)
            ->setParameterPost($request)
        ;
        try {
            $result = Zend_Json::decode($body = $client->request()->getBody());
            isset($result['errorMsg']) and $error = $result['errorMsg'];
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

    /**
     * @return Gri_Api_Helper_Data
     */
    public function getGriApiHeler()
    {
        return Mage::helper('gri_api');
    }

    /**
     * @return Gri_Sales_Helper_Data
     */
    public function getGriSalesHelper()
    {
        return Mage::helper('gri_sales');
    }

    public function getSignature($timestamp)
    {
        return md5($this->getAppKey() . $timestamp . $this->getAppSecret());
    }

    /**
     * B5-2 - cancelExchange
     */
    public function methodCancelExchange()
    {
        if (!$this->getEnabled()) return;
        /* @var $rmas AW_Rma_Model_Mysql4_Entity_Collection */
        $rmas = Mage::getModel('awrma/entity')->getCollection();
        $this->getGriApiHeler()->addRetryCountFilter($rmas);
        // Status 4: Canceled
        // Request Type 1: Exchange
        $rmas->setStatusFilter(4)->setRequestTypeFilter(1);
        /* @var $transactionSave Mage_Core_Model_Resource_Transaction */
        $transactionSave = Mage::getModel('core/resource_transaction');
        /* @var $commentHelper AW_Rma_Helper_Comments */
        $commentHelper = Mage::helper('awrma/comments');
        /* @var $rma AW_Rma_Model_Entity */
        foreach ($rmas as $rma) {
            $order = $rma->getOrder();
            $time = $rma->getUpdatedAt();

            $comments = array();
            /* @var $comment AW_Rma_Model_Entitycomments */
            foreach ($rma->getComments() as $comment) {
                if (($owner = $comment->getOwner()) == AW_Rma_Model_Source_Owner::SYSTEM) continue;
                $comments[] = AW_Rma_Model_Source_Owner::getOptionLabel($owner) . ': ' .
                    Mage::helper('core')->escapeHtml(str_replace('<br />', "\r\n", $comment->getText()));
            }

            $request = array(
                'exchangeId' => $rma->getTextId(),
                'cancelTime' => $this->getFormattedTime($time),
                'comments' => implode(";\r\n", $comments),
            );

            $result = $this->_invoke(self::METHOD_CANCEL_EXCHANGE, $request);
            if ($result['error']) {
                // TODO Handle errors
                $result['raw'] and $this->getGriApiHeler()->increaseRetryCount($rma);
            }
            else {
                $this->getGriApiHeler()->resetRetryCount($rma, FALSE);
                // Status 8: Canceled BDT Notified
                $rma->setStatus(8);
                $order->addStatusHistoryComment('B5-2: Cancel Exchange')
                    ->setIsCustomerNotified(FALSE);
                $transactionSave->addObject($rma)->addObject($order);
                $commentHelper::postComment($rma->getId(), $commentHelper->__('RMA return cancellation request sent to BDT.'), array(
                    'owner' => AW_Rma_Model_Source_Owner::SYSTEM,
                ), FALSE);
            }
        }

        $transactionSave->save();
    }

    /**
     * B2 - cancelOrder
     */
    public function methodCancelOrder()
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
            $items = array();
            /* @var $item Mage_Sales_Model_Order_Creditmemo_Item */
            foreach ($creditmemo->getAllItems() as $item) {
                if ($item->getOrderItem()->getParentItemId()) continue;
                $items[] = array(
                    'orderItemId' => $item->getOrderItemId(),
                    'quantity' => intval($item->getQty()),  //
                );
            }

            $request = array(
                'orderId' => $order->getIncrementId(),
                'orderItems' => $items,
                'cancelTime' => $this->getFormattedTime($time),
                'comments' => implode(";\r\n", $comments),
            );
            $result = $this->_invoke(self::METHOD_CANCEL_ORDER, $request);
            if ($result['error']) {
                // TODO Handle errors
                $result['raw'] and $this->getGriApiHeler()->increaseRetryCount($creditmemo);
            }
            else {
                $this->getGriApiHeler()->resetRetryCount($creditmemo, FALSE);
                $creditmemo->setState($creditmemo::STATE_NOTIFIED);
                $order->addStatusHistoryComment('B2: Cancel Order')
                    ->setIsCustomerNotified(FALSE);
                $transactionSave->addObject($creditmemo)->addObject($order);
            }
        }
        $this->methodCreateRefund($creditmemos);
        $transactionSave->save();
    }

    /**
     * B4-3 - cancelRefund
     */
    public function methodCancelRefund()
    {
        if (!$this->getEnabled()) return;
        $creditmemos = Mage::getModel('sales/order_creditmemo')->getCollection();
        $this->getGriApiHeler()->addRetryCountFilter($creditmemos);
        $creditmemos->getFiltered(array(
            'state' => Gri_Sales_Model_Order_Creditmemo::STATE_CANCELED,
        ));
        /* @var $transactionSave Mage_Core_Model_Resource_Transaction */
        $transactionSave = Mage::getModel('core/resource_transaction');
        /* @var $creditmemo Gri_Sales_Model_Order_Creditmemo */
        foreach ($creditmemos as $creditmemo) {
            $order = $creditmemo->getOrder();
            $time = $creditmemo->getUpdatedAt();

            $request = array(
                'refundId' => $creditmemo->getIncrementId(),
                'cancelTime' => $this->getFormattedTime($time),
            );
            $result = $this->_invoke(self::METHOD_CANCEL_REFUND, $request);
            if ($result['error']) {
                // TODO Handle errors
                $result['raw'] and $this->getGriApiHeler()->increaseRetryCount($creditmemo);
            }
            else {
                $this->getGriApiHeler()->resetRetryCount($creditmemo, FALSE);
                $creditmemo->addComment($this->getGriSalesHelper()->__('Refund cancellation sent to BDT'), FALSE, FALSE, 'bdt')
                    ->setState($creditmemo::STATE_CANCELED_CLOSED);
                $order->addStatusHistoryComment('B4-3: Cancel Refund')
                    ->setIsCustomerNotified(FALSE);
                $transactionSave->addObject($creditmemo)->addObject($order);
            }
        }
        $transactionSave->save();
    }

    /**
     * B3-2 - cancelReturn
     */
    public function methodCancelReturn()
    {
        if (!$this->getEnabled()) return;
        /* @var $rmas AW_Rma_Model_Mysql4_Entity_Collection */
        $rmas = Mage::getModel('awrma/entity')->getCollection();
        $this->getGriApiHeler()->addRetryCountFilter($rmas);
        // Status 4: Canceled
        // Request Type 2: Return
        $rmas->setStatusFilter(4)->setRequestTypeFilter(2);
        /* @var $transactionSave Mage_Core_Model_Resource_Transaction */
        $transactionSave = Mage::getModel('core/resource_transaction');
        /* @var $commentHelper AW_Rma_Helper_Comments */
        $commentHelper = Mage::helper('awrma/comments');
        /* @var $rma AW_Rma_Model_Entity */
        foreach ($rmas as $rma) {
            $order = $rma->getOrder();
            $time = $rma->getUpdatedAt();

            $request = array(
                'rmaId' => $rma->getTextId(),
                'cancelTime' => $this->getFormattedTime($time),
            );
            $result = $this->_invoke(self::METHOD_CANCEL_RETURN, $request);
            if ($result['error']) {
                // TODO Handle errors
                $result['raw'] and $this->getGriApiHeler()->increaseRetryCount($rma);
            }
            else {
                $this->getGriApiHeler()->resetRetryCount($rma, FALSE);
                // Status 8: Canceled BDT Notified
                $rma->setStatus(8);
                $order->addStatusHistoryComment('B3-2: Cancel Return')
                    ->setIsCustomerNotified(FALSE);
                $transactionSave->addObject($rma)->addObject($order);
                $commentHelper::postComment($rma->getId(), $commentHelper->__('RMA return cancellation request sent to BDT.'), array(
                    'owner' => AW_Rma_Model_Source_Owner::SYSTEM,
                ), FALSE);
            }
        }

        $transactionSave->save();
    }

    /**
     * B4-4 - closeRefund
     */
    public function methodCloseRefund()
    {
        if (!$this->getEnabled()) return;
        $creditmemos = Mage::getModel('sales/order_creditmemo')->getCollection();
        $this->getGriApiHeler()->addRetryCountFilter($creditmemos);
        $creditmemos->getFiltered(array(
            'state' => Gri_Sales_Model_Order_Creditmemo::STATE_REFUNDED,
        ));
        /* @var $transactionSave Mage_Core_Model_Resource_Transaction */
        $transactionSave = Mage::getModel('core/resource_transaction');
        /* @var $creditmemo Gri_Sales_Model_Order_Creditmemo */
        foreach ($creditmemos as $creditmemo) {
            $order = $creditmemo->getOrder();
            $time = $creditmemo->getUpdatedAt();

            $request = array(
                'refundId' => $creditmemo->getIncrementId(),
                'closeTime' => $this->getFormattedTime($time),
            );
            $result = $this->_invoke(self::METHOD_CLOSE_REFUND, $request);
            if ($result['error']) {
                // TODO Handle errors
                $result['raw'] and $this->getGriApiHeler()->increaseRetryCount($creditmemo);
            }
            else {
                $this->getGriApiHeler()->resetRetryCount($creditmemo, FALSE);
                $creditmemo->addComment($this->getGriSalesHelper()->__('Refund closed'), FALSE, FALSE, 'bdt')
                    ->setState($creditmemo::STATE_CLOSED);
                $order->addStatusHistoryComment('B4-4: Close Refund')
                    ->setIsCustomerNotified(FALSE);
                $transactionSave->addObject($creditmemo)->addObject($order);
            }
        }
        $transactionSave->save();
    }

    /**
     * B5-1 - createExchange
     */
    public function methodCreateExchange()
    {
        if (!$this->getEnabled()) return;
        /* @var $rmas AW_Rma_Model_Mysql4_Entity_Collection */
        $rmas = Mage::getModel('awrma/entity')->getCollection();
        $this->getGriApiHeler()->addRetryCountFilter($rmas);
        // Status 2: Approved; 3: Package sent
        // Request Type 1: Exchange
        $rmas->setStatusFilter(array(2, 3))->setRequestTypeFilter(1);
        /* @var $transactionSave Mage_Core_Model_Resource_Transaction */
        $transactionSave = Mage::getModel('core/resource_transaction');
        /* @var $commentHelper AW_Rma_Helper_Comments */
        $commentHelper = Mage::helper('awrma/comments');
        /* @var $rma AW_Rma_Model_Entity */
        foreach ($rmas as $rma) {
            $rma->load(NULL);
            $order = $rma->getOrder();
            $time = $rma->getUpdatedAt();
            $rmaId = $rma->getTextId();

            $comments = array();
            /* @var $comment AW_Rma_Model_Entitycomments */
            foreach ($rma->getComments() as $comment) {
                if (($owner = $comment->getOwner()) == AW_Rma_Model_Source_Owner::SYSTEM) continue;
                $comments[] = AW_Rma_Model_Source_Owner::getOptionLabel($owner) . ': ' .
                    Mage::helper('core')->escapeHtml(str_replace('<br />', "\r\n", $comment->getText()));
            }

            $items = array();
            $exchangeItems = $rma->getExchangeItems();
            // RMA Items is an array stored in field order_items
            foreach ($rma->getOrderItems() as $itemId => $qty) {

                $items[] = array(
                    'exchangeItemId' => $rmaId . $itemId,
                    'againstOrderItemId' => $itemId,
                    'newSku' => isset($exchangeItems[$itemId]) ? $exchangeItems[$itemId] : $order->getItemById($itemId)->getSku(),
                    'quantity' => $qty,
                    'reason' => $comments[0],
                    'comments' => implode(";\r\n", $comments)
                );
            }

            $request = array(
                'exchangeId' => $rmaId,
                'againstOrderId' => $order->getIncrementId(),
                'exchangeItems' => $items,
                'createTime' => $this->getFormattedTime($time)
                //'reason' => '',
                //'comments' => implode(";\r\n", $comments)
            );
            $result = $this->_invoke(self::METHOD_CREATE_EXCHANGE, $request);
            if ($result['error']) {
                // TODO Handle errors
                $result['raw'] and $this->getGriApiHeler()->increaseRetryCount($rma);
            }
            else {
                $this->getGriApiHeler()->resetRetryCount($rma, FALSE);
                // Status 7: BDT Notified
                $rma->setStatus(7);
                $order->addStatusHistoryComment('B5-1: Create Exchange')
                    ->setIsCustomerNotified(FALSE);
                $transactionSave->addObject($rma)->addObject($order);
                $commentHelper::postComment($rma->getId(), $commentHelper->__('RMA exchange request sent to BDT.'), array(
                    'owner' => AW_Rma_Model_Source_Owner::SYSTEM,
                ), FALSE);
            }
        }

        $transactionSave->save();
    }

    /**
     * B4-1 - createRefund
     */
    public function methodCreateRefund($creditmemos = NULL)
    {
        if (!$this->getEnabled()) return;
        /* @var $creditmemos Mage_Sales_Model_Resource_Order_Creditmemo_Collection */
        if (!$creditmemos instanceof Mage_Sales_Model_Resource_Order_Creditmemo_Collection) {
            $creditmemos = Mage::getModel('sales/order_creditmemo')->getCollection();
            $creditmemos->getFiltered(array(
                'state' => array(
                    Gri_Sales_Model_Order_Creditmemo::STATE_OPEN,
                    // Consider refunded but not notified creditmemos
                    Gri_Sales_Model_Order_Creditmemo::STATE_REFUNDED,
                ),
                'notified' => 0,
            ));
            $this->getGriApiHeler()->addRetryCountFilter($creditmemos);
        }
        /* @var $transactionSave Mage_Core_Model_Resource_Transaction */
        $transactionSave = Mage::getModel('core/resource_transaction');
        /* @var $creditmemo Gri_Sales_Model_Order_Creditmemo */
        foreach ($creditmemos as $creditmemo) {
            $order = $creditmemo->getOrder();
            $time = $creditmemo->getCreatedAt();
            $items = array();
            /* @var $item Mage_Sales_Model_Order_Creditmemo_Item */
            foreach ($creditmemo->getAllItems() as $item) {
                if ($item->getOrderItem()->getParentItemId()) continue;
                $items[] = array(
                    'refundItemId' => $item->getId(),
                    'orderItemId' => $item->getOrderItemId(),
                    'creditAmount' => $item->getRowTotal(),
                    'adjustment' => '0.00',
                );
            }

            $request = array(
                'refundId' => $creditmemo->getIncrementId(),
                'orderId' => $order->getIncrementId(),
                'refundItems' => $items,
                'creditAmount' => $creditmemo->getGrandTotal(),
                'adjustment' => $creditmemo->getAdjustment(),
                'createTime' => $this->getFormattedTime($time),
            );
            $result = $this->_invoke(self::METHOD_CREATE_REFUND, $request);
            if ($result['error']) {
                // TODO Handle errors
                $result['raw'] and $this->getGriApiHeler()->increaseRetryCount($creditmemo);
            }
            else {
                $this->getGriApiHeler()->resetRetryCount($creditmemo, FALSE);
                $creditmemo->addComment($this->getGriSalesHelper()->__('Refund request sent to BDT'), FALSE, FALSE, 'bdt')
                    ->setNotified(1);
                // Keep state "refunded"
                $creditmemo->getState() == $creditmemo::STATE_REFUNDED or
                    $creditmemo->setState($creditmemo::STATE_NOTIFIED);
                $order->addStatusHistoryComment('B4-1: Create Refund')
                    ->setIsCustomerNotified(FALSE);
                $transactionSave->addObject($creditmemo)->addObject($order);
            }
        }
        $transactionSave->save();
    }

    /**
     * B3-1 - createReturn
     */
    public function methodCreateReturn()
    {
        if (!$this->getEnabled()) return;
        /* @var $rmas AW_Rma_Model_Mysql4_Entity_Collection */
        $rmas = Mage::getModel('awrma/entity')->getCollection();
        $this->getGriApiHeler()->addRetryCountFilter($rmas);
        // Status 2: Approved; 3: Package sent
        // Request Type 2: Return
        $rmas->setStatusFilter(array(2, 3))->setRequestTypeFilter(2);
        /* @var $transactionSave Mage_Core_Model_Resource_Transaction */
        $transactionSave = Mage::getModel('core/resource_transaction');
        /* @var $commentHelper AW_Rma_Helper_Comments */
        $commentHelper = Mage::helper('awrma/comments');
        /* @var $rma AW_Rma_Model_Entity */
        foreach ($rmas as $rma) {
            $rma->load(NULL);
            $order = $rma->getOrder();
            $time = $rma->getUpdatedAt();
            $rmaId = $rma->getTextId();

            $comments = array();
            /* @var $comment AW_Rma_Model_Entitycomments */
            foreach ($rma->getComments() as $comment) {
                if (($owner = $comment->getOwner()) == AW_Rma_Model_Source_Owner::SYSTEM) continue;
                $comments[] = AW_Rma_Model_Source_Owner::getOptionLabel($owner) . ': ' .
                    Mage::helper('core')->escapeHtml(str_replace('<br />', "\r\n", $comment->getText()));
            }

            $items = array();
            // RMA Items is an array stored in field order_items
            foreach ($rma->getOrderItems() as $itemId => $qty) {
                $items[] = array(
                    'rmaItemId' => $rmaId . $itemId,
                    'orderItemId' => $itemId,
                    'quantity' => $qty,
                    'reason' => $comments[0],
                    'comments' => implode(";\r\n", $comments)
                );
            }

            $request = array(
                'rmaId' => $rmaId,
                'orderId' => $order->getIncrementId(),
                'rmaitems' => $items,
                'createTime' => $this->getFormattedTime($time)
            );
            $result = $this->_invoke(self::METHOD_CREATE_RETURN, $request);
            if ($result['error']) {
                // TODO Handle errors
                $result['raw'] and $this->getGriApiHeler()->increaseRetryCount($rma);
            }
            else {
                $this->getGriApiHeler()->resetRetryCount($rma, FALSE);
                // Status 7: BDT Notified
                $rma->setStatus(7);
                $order->addStatusHistoryComment('B3-1: Create Return')
                    ->setIsCustomerNotified(FALSE);
                $transactionSave->addObject($rma)->addObject($order);
                $commentHelper::postComment($rma->getId(), $commentHelper->__('RMA return request sent to BDT.'), array(
                    'owner' => AW_Rma_Model_Source_Owner::SYSTEM,
                ), FALSE);
            }
        }

        $transactionSave->save();
    }

    /**
     * B1 - notifyNewOrder
     */
    public function methodNotifyNewOrder()
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
            $request = array(
                'orderId' => $order->getIncrementId(),
                'payTime' => $this->getFormattedTime($time),
                'status' => $order->getStatus(),
            );
            $result = $this->_invoke(self::METHOD_NOTIFY_NEW_ORDER, $request);
            if ($result['error']) {
                // TODO Handle errors
                $result['raw'] and $this->getGriApiHeler()->increaseRetryCount($order);
            }
            else {
                $this->getGriApiHeler()->resetRetryCount($order, FALSE);
                $order->addStatusHistoryComment('B1: Notify New Order', 'notified')
                    ->setIsCustomerNotified(FALSE);
                $transactionSave->addObject($order);
            }
        }
        $transactionSave->save();
    }

    /**
     * B4-2 - updateRefund
     */
    public function methodUpdateRefund()
    {
        if (!$this->getEnabled()) return;
        /* @var $creditmemos Mage_Sales_Model_Resource_Order_Creditmemo_Collection */
        $creditmemos = Mage::getModel('sales/order_creditmemo')->getCollection();
        $this->getGriApiHeler()->addRetryCountFilter($creditmemos);
        $creditmemos->getFiltered(array(
            'state' => Gri_Sales_Model_Order_Creditmemo::STATE_UPDATED,
        ));
        /* @var $transactionSave Mage_Core_Model_Resource_Transaction */
        $transactionSave = Mage::getModel('core/resource_transaction');
        /* @var $creditmemo Gri_Sales_Model_Order_Creditmemo */
        foreach ($creditmemos as $creditmemo) {
            $order = $creditmemo->getOrder();
            $time = $creditmemo->getCreatedAt();
            $items = array();
            /* @var $item Mage_Sales_Model_Order_Creditmemo_Item */
            foreach ($creditmemo->getAllItems() as $item) {
                if ($item->getOrderItem()->getParentItemId()) continue;
                $items[] = array(
                    'refundItemId' => $item->getId(),
                    'orderItemId' => $item->getOrderItemId(),
                    'creditAmount' => $item->getRowTotal(),
                    'adjustment' => '0.00',
                );
            }

            $request = array(
                'refundId' => $creditmemo->getIncrementId(),
                'orderId' => $order->getIncrementId(),
                'refundItems' => $items,
                'creditAmount' => $creditmemo->getGrandTotal(),
                'adjustment' => $creditmemo->getAdjustment(),
                'createTime' => $this->getFormattedTime($time),
            );
            $result = $this->_invoke(self::METHOD_UPDATE_REFUND, $request);
            if ($result['error']) {
                // TODO Handle errors
                $result['raw'] and $this->getGriApiHeler()->increaseRetryCount($creditmemo);
            }
            else {
                $this->getGriApiHeler()->resetRetryCount($creditmemo, FALSE);
                $creditmemo->addComment($this->getGriSalesHelper()->__('Refund modification sent to BDT'), FALSE, FALSE, 'bdt')
                    ->setState($creditmemo::STATE_NOTIFIED);
                $order->addStatusHistoryComment('B4-2: Update Refund')
                    ->setIsCustomerNotified(FALSE);
                $transactionSave->addObject($creditmemo)->addObject($order);
            }
        }
        $transactionSave->save();
    }
}
