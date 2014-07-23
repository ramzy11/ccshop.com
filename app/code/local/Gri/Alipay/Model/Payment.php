<?php

class Gri_Alipay_Model_Payment extends Mage_Payment_Model_Method_Abstract
{
    const TRADE_STATUS_WAIT_BUYER_PAY = 'WAIT_BUYER_PAY';
    const TRADE_STATUS_WAIT_SELLER_SEND_GOODS = 'WAIT_SELLER_SEND_GOODS';
    const TRADE_STATUS_WAIT_BUYER_CONFIRM_GOODS = 'WAIT_BUYER_CONFIRM_GOODS';
    const TRADE_STATUS_TRADE_FINISHED = 'TRADE_FINISHED';
    const TRADE_STATUS_TRADE_SUCCESS = 'TRADE_SUCCESS';
    const TRADE_STATUS_TRADE_CLOSED = 'TRADE_CLOSED';
    //const TRADE_STATUS_MODIFY_AMOUNT = 'modify.tradeBase.totalFee';

    const REFUND_STATUS_WAIT_SELLER_AGREE = 'WAIT_SELLER_AGREE';
    const REFUND_STATUS_REFUND_SUCCESS = 'REFUND_SUCCESS';
    const REFUND_STATUS_CLOSED = 'CLOSED';

    const DEBUG = TRUE;
    const LOG_MODE = TRUE;

    const GATEWAY_URL = 'https://mapi.alipay.com/gateway.do';

    const LOG_FILE = 'alipay.log';

    protected $_gatewayUrl;
    protected $_code = 'alipay_payment';
    protected $_formBlockType = 'alipay/form';
    protected $_infoBlockType = 'alipay/info';

    // Alipay return codes of payment
    const RETURN_CODE_ACCEPTED = 'paiement';
    const RETURN_CODE_TEST_ACCEPTED = 'payetest';
    const RETURN_CODE_ERROR = 'Annulation';

    // Payment configuration
    protected $_isGateway = FALSE;
    protected $_isInitializeNeeded = TRUE;
    protected $_canAuthorize = TRUE;
    protected $_canCapture = TRUE;
    protected $_canCapturePartial = FALSE;
    protected $_canRefund = FALSE;
    protected $_canVoid = TRUE;
    protected $_canUseInternal = FALSE;
    protected $_canUseCheckout = TRUE;
    protected $_canUseForMultishipping = FALSE;

    protected $_order = NULL;
    protected $_debug = NULL;

    protected $_transactionDetailKeys = array(
        'buyer_email',
        'seller_email',
        'gmt_create',
        'gmt_payment',
        'notify_type',
        'notify_time',
        'trade_status',
        'total_fee',
    );

    /**
     * @param Mage_Sales_Model_Order_Payment $payment
     * @return $this|Mage_Payment_Model_Abstract
     */
    public function cancel(Varien_Object $payment)
    {
        return $this->void($payment);
    }

    public function getOrderPlaceRedirectUrl() {
        return Mage::getUrl('alipay/payment/redirect');
    }

    public function getGatewayUrl() {
        if ($this->_gatewayUrl === NULL) {
            $this->_gatewayUrl = $this->getConfigData('gateway');
            $this->_gatewayUrl or $this->_gatewayUrl = self::GATEWAY_URL;
        }
        return $this->_gatewayUrl;
    }

    /**
     * @return Mage_Checkout_Model_Session
     */
    protected function getCheckout() {
        return Mage::getSingleton('checkout/session');
    }

    public function getStandardCheckoutFormFields() {
        /* @var $order Mage_Sales_Model_Order */
        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId($this->getCheckout()->getLastRealOrderId());
        $this->getCheckout()->setLastOrderId($order->getId());

        $amount = sprintf('%.2f', $order->getBaseGrandTotal());
        if ($order->getBaseCurrencyCode() != 'CNY') {
            if ($convert = Mage::getSingleton('directory/currency')->load($order->getOrderCurrencyCode())) {
                if (!$convert->getRate('CNY'))
                    die('Alipay supports Chinese Yuan Renminbi only, please contact administrator to setup currency rate conversion first.');
                $amount = $convert->convert($amount, "CNY");
                $amount = sprintf('%.2f', $amount);
            }
        }

        // Add trailing string to avoid duplicate order number
        $orderNo = $order->getRealOrderId();
        $this->getConfigData('hash_order_no') and $orderNo .= '-' . dechex(crc32($order->getCreatedAt()));
        $data = array(
            '_input_charset' => 'utf-8',
            'service' => $this->getConfigData('service_type'),
            'partner' => $this->getConfigData('partner_id'),
            'return_url' => Mage::getUrl('alipay/payment/success', array('_secure' => TRUE)),
            'notify_url' => Mage::getUrl('alipay/payment/confirm', array('_secure' => TRUE)),
            'subject' => $orderNo,
            //'body' => $order->getRealOrderId(),
            'out_trade_no' => $orderNo,
//            'price' => $amount,
            'total_fee' => $amount,
            //'show_url' => Mage::getUrl(),
//            'quantity' => '1',
            'payment_type' => '1',
            'logistics_type' => 'EXPRESS',
            'logistics_fee' => '0',
            'logistics_payment' => 'BUYER_PAY',
            'seller_email' => $this->getConfigData('seller_email'),
        );
        switch ($this->getConfigData('use_it_b_pay')) {
            case '1':
                $locale = Mage::app()->getLocale();
                $time = Mage::getStoreConfig(Gri_Sales_Model_Order::CONFIG_PATH_AUTO_CANCELLATION);
                $time = strtr($time, array(',' => ':'));
                $today = $locale->date()->toString(Varien_Date::DATE_INTERNAL_FORMAT);
                $time = $locale->date($today . ' ' . $time, Varien_Date::DATETIME_INTERNAL_FORMAT)->getTimestamp();

                $now = $locale->date()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
                $now = $locale->date($now, Varien_Date::DATETIME_INTERNAL_FORMAT)->getTimestamp();
                $time = $time - $now;
                $time < 0 and $time += 86400;
                $time = (int)($time / 60);
                $data['it_b_pay'] = $time . 'm';
                break;
            case '2':
                $data['it_b_pay'] = $this->getConfigData('it_b_pay');
                break;
        }

        $alipayPayBank = $order->getAlipayPayBank();
        if(!empty($alipayPayBank) && $alipayPayBank != 'ALIPAY') {
            $data['paymethod'] = 'bankPay';
            $data['defaultbank'] = $alipayPayBank;
        }

        $data = $this->wrapData($data);

        $securityCode = trim($this->getConfigData('security_code'));
        $args = "";
        foreach ($data as $k => $v) {
            $args .= $k .'='. $this->charset_encode($v, $data['_input_charset']) .'&';
        }
        $args = substr($args, 0, -1);
        $data['sign'] = $this->getSignCode($args . $securityCode);
        $data['sign_type'] = 'MD5';
        return $data;
    }

    public function initialize($paymentAction, $stateObject)
    {
        $payment = $this->getInfoInstance();
        $payment->setTransactionId($payment->getOrder()->getRealOrderId() . '-' . 'alipay-initialize')
            ->setIsTransactionClosed(0);
        $payment->addTransaction(Mage_Sales_Model_Order_Payment_Transaction::TYPE_AUTH);
        return $this;
    }

    /**
     * Capture payment
     *
     * @param   Varien_Object $payment
     * @param float $amount
     * @return  Gri_Alipay_Model_Payment
     */
    public function capture(Varien_Object $payment, $amount)
    {
        parent::capture($payment, $amount);
        $payment->setStatus(self::STATUS_APPROVED);
        return $this;
    }

    public function confirmPayment($data) {
        if(self::DEBUG) {
            $this->_debug = Mage::getModel('alipay/api_debug');
            $this->_debug->setRequestBody(var_export($data, 1));
            $this->_debug->save();
        }

        if(!$this->verifyNotification($data)) {
            $this->log('Verification failure: ' . var_export($data, 1));
        } else {
            // HERE WE GO
            /* @var $order Mage_Sales_Model_Order */
            $order = Mage::getModel('sales/order');
            list($incrementId) = explode('-',  $data['out_trade_no']);
            $order->loadByIncrementId($incrementId);
            $this->log('Order: ' . $incrementId . ' notification verification OK');
            if(!$order->getId()) {
                // TODO: Missed Order
                $this->log('Order not found: ' . $incrementId);
                return;
            }

            $helper = Mage::helper('alipay');
            $payment_status = trim($data['trade_status']);
            $this->log('Order: ' . $incrementId . ', payment_status: ' . $payment_status);
            switch ($payment_status) {
                case self::TRADE_STATUS_WAIT_BUYER_PAY:
                    $order->addStatusToHistory(
                        Mage_Sales_Model_Order::STATE_PENDING_PAYMENT,
                        $helper->__('Pending buyer payment.')
                    );
                    $order->save();
                    $this->responseSuccess();
                    break;

                case self::TRADE_STATUS_WAIT_SELLER_SEND_GOODS:
                case self::TRADE_STATUS_TRADE_SUCCESS:
                    if(!$order->canInvoice()) {
                        /*$order->addStatusToHistory(
                            $order->getStatus(),
                            $helper->__('Invoice already exists', TRUE),
                            TRUE
                        );
                        $order->save();*/
                    } else {
                        try {
                            // HERE IT IS!
                            $this->log('Order: ' . $incrementId . ', transaction id: ' . $data['trade_no']);
                            $payment = $order->getPayment();
                            $payment->setTransactionId($data['trade_no']);
    //                        foreach ($this->_transactionDetailKeys as $key) {
    //                            isset($data[$key]) and $payment->setTransactionAdditionalInfo($key, $data[$key]);
    //                        }
                            $paymentAccount = isset($data['buyer_email']) ? $data['buyer_email'] : NULL;
                            strpos($paymentAccount, '@') and $order->setPaymentAccount($paymentAccount);
                            $this->log('Order: ' . $incrementId . ', creating invoice');
                            $invoice = $order->prepareInvoice();
                            $this->log('Order: ' . $incrementId . ', invoice created');
                            $invoice->setRequestedCaptureCase('online');
                            $invoice->register();//->pay();
                            $this->log('Order: ' . $incrementId . ' paid');
                            $invoice->save();

                            $order->addStatusToHistory(
                                $order->getStatus(),
                                $helper->__('Invoice #%s created', $invoice->getIncrementId()),
                                TRUE
                            );

                            $order->addStatusToHistory(
                                $this->getConfigData('order_status_payment_accepted'),
                                $helper->__('Waiting for seller send goods.'),
                                TRUE
                            );
                            $order->save();
                        }
                        catch (Exception $e)
                        {
                            $this->log($e->getMessage(), TRUE);
                            $this->log(mageDebugBacktrace(TRUE, FALSE, TRUE));
                            exit;
                        }
                    }
                    $this->log('Order: ' . $incrementId . ', send success sign to Alipay');
                    $this->responseSuccess();
                    break;

                case self::TRADE_STATUS_WAIT_BUYER_CONFIRM_GOODS:
                    $order->addStatusToHistory(
                        $order->getStatus(),
                        $helper->__('Waiting for buyer to confirm goods.')
                    );
                    $order->save();
                    $this->responseSuccess();
                    break;

                case self::TRADE_STATUS_TRADE_FINISHED:
                    $order->addStatusToHistory(
                        $this->getConfigData('order_status_payment_accepted'),
                        Mage::helper('alipay')->__('Trade finished'),
                        TRUE
                    );
                    //$order->setState(
                    //    Mage_Sales_Model_Order::STATE_PROCESSING,
                    //    TRUE,
                //        $helper->__('Trade finished.'),
                //        TRUE
                //    );
                    $order->save();
                    $this->responseSuccess();
                    break;

                case self::TRADE_STATUS_TRADE_CLOSED:
                    $order->setState(
                        Mage_Sales_Model_Order::STATE_CLOSED,
                        TRUE,
                        $helper->__('Trade closed.'),
                        TRUE
                    );
                    $order->save();
                    $this->responseSuccess();
                    break;

                default:
                    $order->addStatusToHistory(
                        $order->getStatus(),
                        $helper->__('Received notification: %s', $payment_status)
                    );
                    $order->save();
                    $this->responseSuccess();
                    break;
            }
        }
    }

    protected function verifyNotification($params) {
        $ret = FALSE;

        // TODO: We'd better to verify sign code

        $data = array(
            '_input_charset' => 'utf-8',
            'service' => 'notify_verify',
            'partner' => $this->getConfigData('partner_id'),
            'notify_id' => $params['notify_id']
        );
        //if(self::DEBUG) $this->_debug->setResponseBody(print_r($data, 1))->save();
        $http = new Varien_Http_Adapter_Curl();
        $http->setConfig(array('verifypeer' => 0));
        $http->write(Zend_Http_Client::GET, $this->getGatewayUrl().'?'.http_build_query($data));
        $response = $http->read();
        $response = preg_split('/^\r?$/m', $response, 2);
        $response = trim($response[1]);
        if(self::DEBUG) $this->_debug->setResponseBody($response)->save();
        if($response == 'true') $ret = TRUE;

        return $ret;
    }

    /**
     * @param Mage_Sales_Model_Order_Payment $payment
     * @return $this|Mage_Payment_Model_Abstract
     */
    public function void(Varien_Object $payment)
    {
        $order = $payment->getOrder();
        $orderNo = $order->getRealOrderId();
        $payment->setTransactionId($orderNo . '-alipay-void');
        $this->getConfigData('hash_order_no') and $orderNo .= '-' . dechex(crc32($order->getCreatedAt()));
        $data = array(
            '_input_charset' => 'utf-8',
            'out_order_no' => $orderNo,
            'partner' => $this->getConfigData('partner_id'),
            'service' => 'close_trade',
        );
        $http = new Varien_Http_Adapter_Curl();
        $http->setConfig(array('verifypeer' => 0));
        $args = '';
        foreach ($data as $k => $v) {
            $args .= $k .'='. $this->charset_encode($v, $data['_input_charset']) .'&';
        }
        $args = substr($args, 0, -1);
        $securityCode = trim($this->getConfigData('security_code'));
        $data['sign'] = $this->getSignCode($args . $securityCode);
        $data['sign_type'] = 'MD5';
        $http->write(Zend_Http_Client::GET, $this->getGatewayUrl() . '?' . http_build_query($data));
        $response = $http->read();
        $response = preg_split('/^\r?$/m', $response, 2);
        $response = trim($response[1]);
        $this->log('Order cancellation: ' . var_export($data, TRUE) . ' with response: ' . $response);
        return $this;
    }

    /**
     * Exclude sign, sign_type, then sort by key
     *
     * @param array $data
     * @return array
     */
    private function wrapData(array $data) {
        $ret = array();
        foreach($data as $k => $v) {
            if($k == 'sign' || $k == 'sign_type') continue;
            $ret[$k] = $v;
        }
        ksort($ret);
        reset($ret);
        return $ret;
    }

    public function getSignCode($str){
        return md5($str);
    }

    public function responseSuccess() {
        die('success');
    }

    /**
     *  Form block description
     *
     *  @return     object
     */
    public function createFormBlock($name) {
        $block = $this->getLayout()->createBlock('alipay/form_payment', $name);
        $block->setMethod($this->_code);
        $block->setPayment($this->getPayment());

        return $block;
    }

    public function charset_encode($input, $_output_charset, $_input_charset = "GBK") {
        $output = "";
        if ($_input_charset == $_output_charset || $input == NULL) {
            $output = $input;
        } elseif (function_exists("mb_convert_encoding")) {
            $output = mb_convert_encoding($input, $_output_charset, $_input_charset);
        } elseif (function_exists("iconv")) {
            $output = iconv($_input_charset, $_output_charset, $input);
        } else
            die("sorry, you have no libs support for charset change.");
        return $output;
    }

    public function log($message, $forceLog = FALSE)
    {
        self::LOG_MODE || $forceLog and Mage::log($message, NULL, self::LOG_FILE);
    }
}
