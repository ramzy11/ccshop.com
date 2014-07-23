<?php

/**
 * Magento Gri Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Gri Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/gri-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Gri
 * @package     Gri_Reward
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/gri-edition
 */

/**
 * Reward model
 *
 * @category    Gri
 * @package     Gri_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Gri_Reward_Model_Reward extends Mage_Core_Model_Abstract {

    const XML_PATH_BALANCE_UPDATE_TEMPLATE = 'gri_reward/notification/balance_update_template';
    const XML_PATH_BALANCE_WARNING_TEMPLATE = 'gri_reward/notification/expiry_warning_template';
    const XML_PATH_EMAIL_IDENTITY = 'gri_reward/notification/email_sender';
    const XML_PATH_MIN_POINTS_BALANCE = 'gri_reward/general/min_points_balance';
    const REWARD_ACTION_ADMIN = 0;
    const REWARD_ACTION_ORDER = 1;
    const REWARD_ACTION_REGISTER = 2;
    const REWARD_ACTION_NEWSLETTER = 3;
    const REWARD_ACTION_INVITATION_CUSTOMER = 4;
    const REWARD_ACTION_INVITATION_ORDER = 5;
    const REWARD_ACTION_REVIEW = 6;
    const REWARD_ACTION_TAG = 7;
    const REWARD_ACTION_ORDER_EXTRA = 8;
    const REWARD_ACTION_CREDITMEMO = 9;
    const REWARD_ACTION_SALESRULE = 10;
    const REWARD_ACTION_REVERT = 11;
    const REWARD_ACTION_ORDER_PLACE = 12;
    const REWARD_ACTION_REDEEM_GIFTCARD = 13;

    protected $_modelLoadedByCustomer = false;
    static protected $_actionModelClasses = array();
    protected $_rates = array();
    protected $_logFile = 'giftcard.log';

    /**
     * Identifies that reward balance was updated or not
     *
     * @var boolean
     */
    protected $_rewardPointsUpdated = false;

    /**
     * Internal constructor
     */
    protected function _construct() {
        parent::_construct();
        $this->_init('gri_reward/reward');
        self::$_actionModelClasses = self::$_actionModelClasses + array(
            self::REWARD_ACTION_ADMIN => 'gri_reward/action_admin',
            self::REWARD_ACTION_ORDER => 'gri_reward/action_order',
            self::REWARD_ACTION_REGISTER => 'gri_reward/action_register',
            self::REWARD_ACTION_NEWSLETTER => 'gri_reward/action_newsletter',
            self::REWARD_ACTION_INVITATION_CUSTOMER => 'gri_reward/action_invitationCustomer',
            self::REWARD_ACTION_INVITATION_ORDER => 'gri_reward/action_invitationOrder',
            self::REWARD_ACTION_REVIEW => 'gri_reward/action_review',
            self::REWARD_ACTION_TAG => 'gri_reward/action_tag',
            self::REWARD_ACTION_ORDER_EXTRA => 'gri_reward/action_orderExtra',
            self::REWARD_ACTION_CREDITMEMO => 'gri_reward/action_creditmemo',
            self::REWARD_ACTION_SALESRULE => 'gri_reward/action_salesrule',
            self::REWARD_ACTION_REVERT => 'gri_reward/action_orderRevert',
            self::REWARD_ACTION_ORDER_PLACE => 'gri_reward/action_orderPlace',
            self::REWARD_ACTION_REDEEM_GIFTCARD => 'gri_reward/action_giftCard'
        );
    }

    /**
     * Set action Id and action model class.
     * Check if given action Id is not integer throw exception
     *
     * @param integer $actionId
     * @param string $actionModelClass
     */
    public static function setActionModelClass($actionId, $actionModelClass) {
        if (!is_int($actionId)) {
            Mage::throwException(Mage::helper('gri_reward')->__('Given action ID has to be an integer value.'));
        }
        self::$_actionModelClasses[$actionId] = $actionModelClass;
    }

    /**
     * Processing object before save data.
     * Load model by customer and website,
     * prepare points data
     *
     * @return Gri_Reward_Model_Reward
     */
    protected function _beforeSave() {
        $this->loadByCustomer()
                ->_preparePointsDelta()
                ->_preparePointsBalance()
                ->_preparePointsIncrement();
        return parent::_beforeSave();
    }

    /**
     * Processing object after save data.
     * Save reward history
     *
     * @return Gri_Reward_Model_Reward
     */
    protected function _afterSave() {
        if ((int) $this->getPointsDelta() != 0 || $this->getCappedReward()) {
            $this->_prepareCurrencyAmount();
            $this->getHistory()
                    ->prepareFromReward()
                    ->save();
            $this->sendBalanceUpdateNotification();
        }
        return parent::_afterSave();
    }

    /**
     * Return instance of action wrapper
     *
     * @param string|int $action Action code or a factory name
     * @return Gri_Reward_Model_Action_Abstract|null
     */
    public function getActionInstance($action, $isFactoryName = false) {
        if ($isFactoryName) {
            $action = array_search($action, self::$_actionModelClasses);
            if (!$action) {
                return null;
            }
        }

        $instance = Mage::registry('_reward_actions' . $action);
        if (!$instance && array_key_exists($action, self::$_actionModelClasses)) {
            $instance = Mage::getModel(self::$_actionModelClasses[$action]);
            // setup invariant properties once
            $instance->setAction($action);
            $instance->setReward($this);
            Mage::register('_reward_actions' . $action, $instance);
        }
        if (!$instance) {
            return null;
        }
        // keep variable properties up-to-date
        $instance->setHistory($this->getHistory());
        if ($this->getActionEntity()) {
            $instance->setEntity($this->getActionEntity());
        }
        return $instance;
    }

    /**
     * Check if can update reward
     *
     * @return boolean
     */
    public function canUpdateRewardPoints() {
        return $this->getActionInstance($this->getAction())->canAddRewardPoints();
    }

    /**
     * Getter
     *
     * @return boolean
     */
    public function getRewardPointsUpdated() {
        return $this->_rewardPointsUpdated;
    }

    /**
     * Save reward points
     *
     * @return Gri_Reward_Model_Reward
     */
    public function updateRewardPoints() {

        $this->_rewardPointsUpdated = false;
        if ($this->canUpdateRewardPoints()) {
            try {
                $this->save();
                Mage::dispatchEvent('gri_reward_update_reward_points', array(
                    'reward' => $this,
                    'customer' => $this->getCustomer(),
                ));
                $this->_rewardPointsUpdated = true;
            } catch (Exception $e) {
                $this->_rewardPointsUpdated = false;
                throw $e;
            }
        }
        return $this;
    }

    /**
     * Setter.
     * Set customer id
     *
     * @param Mage_Customer_Model_Customer $customer
     * @return Gri_Reward_Model_Reward
     */
    public function setCustomer($customer) {
        $this->setData('customer_id', $customer->getId());
        $this->setData('customer_group_id', $customer->getGroupId());
        $this->setData('customer', $customer);
        return $this;
    }

    /**
     * Getter
     *
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomer() {
        if (!$this->_getData('customer') && $this->getCustomerId()) {
            $customer = Mage::getModel('customer/customer')->load($this->getCustomerId());
            $this->setCustomer($customer);
        }
        return $this->_getData('customer');
    }

    /**
     * Getter
     *
     * @return integer
     */
    public function getCustomerGroupId() {
        if (!$this->_getData('customer_group_id') && $this->getCustomer()) {
            $this->setData('customer_group_id', $this->getCustomer()->getGroupId());
        }
        return $this->_getData('customer_group_id');
    }

    /**
     * Getter for website_id
     * If website id not set, get it from assigned store
     *
     * @return int
     */
    public function getWebsiteId() {
        if (!$this->_getData('website_id') && ($store = $this->getStore())) {
            $this->setData('website_id', $store->getWebsiteId());
        }
        return $this->_getData('website_id');
    }

    /**
     * Getter for store (for emails etc)
     * Trying get store from customer if its not assigned
     *
     * @return Mage_Core_Model_Store|null
     */
    public function getStore() {
        $store = null;
        if ($this->hasData('store') || $this->hasData('store_id')) {
            $store = $this->getDataSetDefault('store', $this->_getData('store_id'));
        } elseif ($this->getCustomer() && $this->getCustomer()->getStoreId()) {
            $store = $this->getCustomer()->getStore();
            $this->setData('store', $store);
        }
        if ($store !== null) {
            return is_object($store) ? $store : Mage::app()->getStore($store);
        }
        return $store;
    }

    /**
     * Getter
     *
     * @return integer
     */
    public function getPointsDelta() {
        if ($this->_getData('points_delta') === null) {
            $this->_preparePointsDelta();
        }
        return $this->_getData('points_delta');
    }

    /**
     * Getter.
     * Recalculate currency amount if need.
     *
     * @return float
     */
    public function getCurrencyAmount() {
        if ($this->_getData('currency_amount') === null) {
            $this->_prepareCurrencyAmount();
        }
        return $this->_getData('currency_amount');
    }

    /**
     * Getter.
     * Return formated currency amount in currency of website
     *
     * @return string
     */
    public function getFormatedCurrencyAmount() {
        $currencyAmount = Mage::app()->getLocale()->currency($this->getWebsiteCurrencyCode())
                ->toCurrency($this->getCurrencyAmount());
        return $currencyAmount;
    }

    /**
     * Getter
     *
     * @return string
     */
    public function getWebsiteCurrencyCode() {
        if (!$this->_getData('website_currency_code')) {
            $this->setData('website_currency_code', Mage::app()->getWebsite($this->getWebsiteId())
                            ->getBaseCurrencyCode());
        }
        return $this->_getData('website_currency_code');
    }

    /**
     * Getter
     *
     * @return Gri_Reward_Model_Reward_History
     */
    public function getHistory() {
        if (!$this->_getData('history')) {
            $this->setData('history', Mage::getModel('gri_reward/reward_history'));
            $this->getHistory()->setReward($this);
        }
        return $this->_getData('history');
    }

    /**
     * Initialize and fetch if need rate by given direction
     *
     * @param integer $direction
     * @return Gri_Reward_Model_Reward_Rate
     */
    protected function _getRateByDirection($direction) {
        if (!isset($this->_rates[$direction])) {
            $this->_rates[$direction] = Mage::getModel('gri_reward/reward_rate')
                    ->fetch($this->getCustomerGroupId(), $this->getWebsiteId(), $direction);
        }
        return $this->_rates[$direction];
    }

    /**
     * Return rate depend on action
     *
     * @return Gri_Reward_Model_Reward_Rate
     */
    public function getRate() {
        return $this->_getRateByDirection($this->getRateDirectionByAction());
    }

    /**
     * Return rate to convert points to currency amount
     *
     * @return Gri_Reward_Model_Reward_Rate
     */
    public function getRateToCurrency() {
        return $this->_getRateByDirection(Gri_Reward_Model_Reward_Rate::RATE_EXCHANGE_DIRECTION_TO_CURRENCY);
    }

    /**
     * Return rate to convert currency amount to points
     *
     * @return Gri_Reward_Model_Reward_Rate
     */
    public function getRateToPoints() {
        return $this->_getRateByDirection(Gri_Reward_Model_Reward_Rate::RATE_EXCHANGE_DIRECTION_TO_POINTS);
    }

    /**
     * Return rate direction by action
     *
     * @return integer
     */
    public function getRateDirectionByAction() {
        switch ($this->getAction()) {
            case self::REWARD_ACTION_ORDER_EXTRA:
                $direction = Gri_Reward_Model_Reward_Rate::RATE_EXCHANGE_DIRECTION_TO_POINTS;
                break;
            default:
                $direction = Gri_Reward_Model_Reward_Rate::RATE_EXCHANGE_DIRECTION_TO_CURRENCY;
                break;
        }
        return $direction;
    }

    /**
     * Load by customer and website
     *
     * @return Gri_Reward_Model_Reward
     */
    public function loadByCustomer() {
        if (!$this->_modelLoadedByCustomer && $this->getCustomerId()
                && $this->getWebsiteId()) {
            $this->getResource()->loadByCustomerId($this, $this->getCustomerId(), $this->getWebsiteId());
            $this->_modelLoadedByCustomer = true;
        }
        return $this;
    }

    /**
     * Estimate available points reward for specified action
     *
     * @param Gri_Reward_Model_Action_Abstract $action
     * @return int|null
     */
    public function estimateRewardPoints(Gri_Reward_Model_Action_Abstract $action) {
        $websiteId = $this->getWebsiteId();
        $uncappedPts = (int) $action->getPoints($websiteId);
        $max = (int) Mage::helper('gri_reward')->getGeneralConfig('max_points_balance', $websiteId);
        if ($max > 0) {
            return min(max($max - (int) $this->getPointsBalance(), 0), $uncappedPts);
        }
        return $uncappedPts;
    }

    /**
     * Estimate available monetary reward for specified action
     * May take points value or automatically determine from action
     *
     * @param Gri_Reward_Model_Action_Abstract $action
     * @return float|null
     */
    public function estimateRewardAmount(Gri_Reward_Model_Action_Abstract $action) {
        if (!$this->getCustomerId()) {
            return null;
        }
        $websiteId = $this->getWebsiteId();
        $rate = $this->getRateToCurrency();
        if (!$rate->getId()) {
            return null;
        }
        return $rate->calculateToCurrency($this->estimateRewardPoints($action), false);
    }

    /**
     * Prepare points delta, get points delta from config by action
     *
     * @return Gri_Reward_Model_Reward
     */
    protected function _preparePointsDelta() {
        $delta = 0;
        $action = $this->getActionInstance($this->getAction());
        if ($action !== null) {
            $delta = $action->getPoints($this->getWebsiteId());
        }
        if ($delta) {
            if ($this->hasPointsDelta()) {
                $delta = $delta + $this->getPointsDelta();
            }
            $this->setPointsDelta((int) $delta);
        }
        return $this;
    }

    /**
     * Prepare points balance
     *
     * @return Gri_Reward_Model_Reward
     */
    protected function _preparePointsBalance() {
        $points = 0;
        if ($this->hasPointsDelta()) {
            $points = $this->getPointsDelta();
        }
        $pointsBalance = 0;
        $pointsBalance = (int) $this->getPointsBalance() + $points;
        $maxPointsBalance = (int) (Mage::helper('gri_reward')
                        ->getGeneralConfig('max_points_balance', $this->getWebsiteId()));
        if ($maxPointsBalance != 0 && ($pointsBalance > $maxPointsBalance)) {
            $pointsBalance = $maxPointsBalance;
            $pointsDelta = $maxPointsBalance - (int) $this->getPointsBalance();
            $croppedPoints = (int) $this->getPointsDelta() - $pointsDelta;
            $this->setPointsDelta($pointsDelta)
                    ->setIsCappedReward(true)
                    ->setCroppedPoints($croppedPoints);
        }
        $this->setPointsBalance($pointsBalance);
        return $this;
    }

    /**
     * Prepare  points Increment
     *
     *  @return Gri_Reward_Model_Reward
     */
    protected function _preparePointsIncrement() {
        $points = 0;
        if ($this->hasPointsDelta() > 0) {
            $points = $this->getPointsDelta();
            $pointsIncrement = 0;
            $pointsIncrement = (int) $this->getPointsIncrement() + $points;
            $this->setPointsIncrement($pointsIncrement);
        }

        return $this;
    }

    /**
     * Prepare currency amount and currency delta
     *
     * @return Gri_Reward_Model_Reward
     */
    protected function _prepareCurrencyAmount() {
        $amount = 0;
        $amountDelta = 0;
        if ($this->hasPointsDelta()) {
            $amountDelta = $this->_convertPointsToCurrency($this->getPointsDelta());
        }
        $amount = $this->_convertPointsToCurrency($this->getPointsBalance());
        $this->setCurrencyDelta((float) $amountDelta);
        $this->setCurrencyAmount((float) ($amount));
        return $this;
    }

    /**
     * Convert points to currency
     *
     * @param integer $points
     * @return float
     */
    protected function _convertPointsToCurrency($points) {
        $ammount = 0;
        if ($points && $this->getRateToCurrency()) {
            $ammount = $this->getRateToCurrency()->calculateToCurrency($points);
        }
        return (float) $ammount;
    }

    /**
     * Check is enough points (currency amount) to cover given amount
     *
     * @param float $amount
     * @return boolean
     */
    public function isEnoughPointsToCoverAmount($amount) {
        if ($this->getId() && $this->getCurrencyAmount() >= $amount) {
            return true;
        }
        return false;
    }

    /**
     * Return points equivalent of given amount.
     * Converting by 'to currency' rate and points round up
     *
     * @param float $amount
     * @return integer
     */
    public function getPointsEquivalent($amount) {
        $points = 0;
        if (!$amount) {
            return $points;
        }

        $ratePointsCount = $this->getRateToCurrency()->getPoints();
        $rateCurrencyAmount = $this->getRateToCurrency()->getCurrencyAmount();
        if ($rateCurrencyAmount > 0) {
            $delta = $amount / $rateCurrencyAmount;
            if ($delta > 0) {
                $points = $ratePointsCount * ceil($delta);
            }
        }

        return $points;
    }

    /**
     * Send Balance Update Notification to customer if notification is enabled
     *
     * @return Gri_Reward_Model_Reward
     */
    public function sendBalanceUpdateNotification() {
        if (!$this->getCustomer()->getRewardUpdateNotification()) {
            return $this;
        }
        $delta = (int) $this->getPointsDelta();
        if ($delta == 0) {
            return $this;
        }
        $history = $this->getHistory();
        $store = Mage::app()->getStore($this->getStore());
        $mail = Mage::getModel('core/email_template');
        /* @var $mail Mage_Core_Model_Email_Template */
        $mail->setDesignConfig(array('area' => 'frontend', 'store' => $store->getId()));
        $templateVars = array(
            'store' => $store,
            'customer' => $this->getCustomer(),
            'unsubscription_url' => Mage::helper('gri_reward/customer')
                    ->getUnsubscribeUrl('update', $store->getId()),
            'points_balance' => $this->getPointsBalance(),
            'reward_amount_was' => Mage::helper('gri_reward')->formatAmount(
                    $this->getCurrencyAmount() - $history->getCurrencyDelta()
                    , true, $store->getStoreId()),
            'reward_amount_now' => Mage::helper('gri_reward')->formatAmount(
                    $this->getCurrencyAmount()
                    , true, $store->getStoreId()),
            'reward_pts_was' => ($this->getPointsBalance() - $delta),
            'reward_pts_change' => $delta,
            'update_message' => $this->getHistory()->getMessage(),
            'update_comment' => $history->getComment()
        );
        $mail->sendTransactional(
                $store->getConfig(self::XML_PATH_BALANCE_UPDATE_TEMPLATE), $store->getConfig(self::XML_PATH_EMAIL_IDENTITY), $this->getCustomer()->getEmail(), null, $templateVars, $store->getId()
        );
        if ($mail->getSentSuccess()) {
            $this->setBalanceUpdateSent(true);
        }
        return $this;
    }

    /**
     * Send low Balance Warning Notification to customer if notification is enabled
     *
     * @param Gri_Reward_Model_Reward_History $history
     * @return Gri_Reward_Model_Reward
     * @see Gri_Reward_Model_Mysql4_Reward_History_Collection::loadExpiredSoonPoints()
     */
    public function sendBalanceWarningNotification($item, $websiteId) {
        $mail = Mage::getModel('core/email_template');
        /* @var $mail Mage_Core_Model_Email_Template */
        $mail->setDesignConfig(array('area' => 'frontend', 'store' => $item->getStoreId()));
        $store = Mage::app()->getStore($item->getStoreId());
        $amount = Mage::helper('gri_reward')
                ->getRateFromRatesArray($item->getPointsBalanceTotal(), $websiteId, $item->getCustomerGroupId());
        $action = Mage::getSingleton('gri_reward/reward')->getActionInstance($item->getAction());
        $templateVars = array(
            'store' => $store,
            'customer_name' => $item->getCustomerFirstname() . ' ' . $item->getCustomerLastname(),
            'unsubscription_url' => Mage::helper('gri_reward/customer')->getUnsubscribeUrl('warning'),
            'remaining_days' => $store->getConfig('gri_reward/notification/expiry_day_before'),
            'points_balance' => $item->getPointsBalanceTotal(),
            'points_expiring' => $item->getTotalExpired(),
            'reward_amount_now' => Mage::helper('gri_reward')->formatAmount($amount, true, $item->getStoreId()),
            'update_message' => ($action !== null ? $action->getHistoryMessage($item->getAdditionalData()) : '')
        );
        $mail->sendTransactional(
                $store->getConfig(self::XML_PATH_BALANCE_WARNING_TEMPLATE), $store->getConfig(self::XML_PATH_EMAIL_IDENTITY), $item->getCustomerEmail(), null, $templateVars, $store->getId()
        );
        return $this;
    }

    /**
     * Prepare orphan points by given website id and website base currency code
     * after website was deleted
     *
     * @param integer $websiteId
     * @param string $baseCurrencyCode
     * @return Gri_Reward_Model_Reward
     */
    public function prepareOrphanPoints($websiteId, $baseCurrencyCode) {
        if ($websiteId) {
            $this->_getResource()->prepareOrphanPoints($websiteId, $baseCurrencyCode);
        }
        return $this;
    }

    /**
     * Delete orphan (points of deleted website) points by given customer
     *
     * @param Mage_Customer_Model_Customer | integer | null $customer
     * @return Gri_Reward_Model_Reward
     */
    public function deleteOrphanPointsByCustomer($customer = null) {
        if ($customer === null) {
            $customer = $this->getCustomerId() ? $this->getCustomerId() : $this->getCustomer();
        }
        if (is_object($customer) && $customer instanceof Mage_Customer_Model_Customer) {
            $customer = $customer->getId();
        }
        if ($customer) {
            $this->_getResource()->deleteOrphanPointsByCustomer($customer);
        }
        return $this;
    }

    /**
     *  Override setter for setting customer group id  from order
     *
     *  @param mixed $entity
     *  @return Gri_Reward_Model_Reward
     */
    public function setActionEntity($entity) {
        if ($entity->getCustomerGroupId()) {
            $this->setCustomerGroupId($entity->getCustomerGroupId());
        }
        return parent::setData('action_entity', $entity);
    }

    public function getCustomerPointsBalance() {
        $customer_id = Mage::getSingleton('customer/session')->getCustomer()->getId();
        if ($customer_id) {
            return $this->load($customer_id, 'customer_id')->getPointsBalance();
        }
        return 0;
    }

    /**
     *  @param  $order
     *
     *  @return  int
     */
    public function convertGiftCardPointsToAmountByOrder($order) {
        // quote id
        $quoteId = $order->getQuoteId();
        // quote
        $quote = Mage::getModel('sales/quote')->setStoreId($order->getStoreId())->load($quoteId);
        //quote  items
        $items = Mage::getModel('sales/quote_item')->getCollection()
                ->setQuote($quote)
                ->addFieldToSelect('*')
                ->addFieldToFilter('quote_id', $quoteId);

        $reward_points_balance = $order->getRewardPointsBalance();
        $points_product = 0;
        $card_qty = 0;

        foreach ($items as $item) {
            /* @var  Mage_Catalog_Model_Product */
            $product = $item->getProduct();

            //item qty
            $card_qty += $item->getQty();

            $giftproductAttributeSetId = Mage::getSingleton('gri_sales/quote')->getAttributeSetIdByName('gifts');
            if ($product->getAttributeSetId() == $giftproductAttributeSetId) {
                $points_product += $product->getRewardPoints() * $item->getQty();
            }
        }

        $points_card = intval(($reward_points_balance - $points_product) / $card_qty);

        //card base price
        return $this->_convertPointsToCurrency($points_card);
    }

    /**
     *  @param int $reward_points
     *
     */
    public function convertRewardPointsToAmountBalance($reward_points) {
        if (intval($reward_points) <= 0) {
            return false;
        }

        $reward = Mage::getSingleton('gri_reward/reward');
    }

    /**
     *  @param int  $attribute_set_id
     *
     */
    protected function getAttributeCode($attribute_set_id) {
        // return  Mage::getResourceSingleton('eav/entity_attribute_set');
        $sql = " SELECT * FROM `eav_attribute_set` WHERE `attribute_set_id`='" . intval($attribute_set_id) . "'";
        $result = $this->getReadAdapter()->query($sql);
        $result = $result->fetch(PDO::FETCH_OBJ);

        return strtolower($result->attribute_set_name) ? strtolower($result->attribute_set_name) : null;
    }

    /**
     * read adapter
     */
    public function getReadAdapter() {
        return Mage::getSingleton('core/resource')->getConnection('core_read');
    }
}
