<?php

class Gri_SalesRule_Model_Observer extends Varien_Object
{
    /**
     * Discount calculation object
     *
     * @var Gri_SalesRule_Model_Validator
     */
    protected $_calculator;
    /*protected $_reservedAttributes = array(
        'attribute_set_id',
        'entity_type_id',
        'entity_id',
        'type_id',
        'url_key',
        'sku',
        'url_path',
        'category_ids',
        'visibility',
        'preorder_from_date',
        'preorder_to_date',
        'news_from_date',
        'news_to_date',
        'best_seller',
        'editors_pick',
        'group_price',
        'special_from_date',
        'special_to_date',
        'image',
        'small_image',
        'thumbnail',
        'media_gallery',
        'gallery',
    );*/

    public function __construct()
    {
        $this->_calculator = Mage::getSingleton('salesrule/validator');
    }

    /**
     * Following address data fields will be added after collect totals:
    array (
        'recurring_initial_fee_amount',
        'base_recurring_initial_fee_amount',
        'cached_items_all',
        'cached_items_nominal',
        'cached_items_nonnominal',
        'recurring_trial_payment_amount',
        'base_recurring_trial_payment_amount',
        'nominal_subtotal_amount',
        'base_nominal_subtotal_amount',
        'total_qty',
        'base_virtual_amount',
        'virtual_amount',
        'base_subtotal_incl_tax',
        'nominal_discount_amount',
        'base_nominal_discount_amount',
        'nominal_weee_amount',
        'base_nominal_weee_amount',
        'nominal_tax_amount',
        'base_nominal_tax_amount',
        'freeshipping_amount',
        'base_freeshipping_amount',
    )
     * Prepare total_qty before validation
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @return $this
     */
    protected function _prepareAddressTotals($address)
    {
        $address->setTotalQty(0);
        $items = $address->getAllNonNominalItems();
        foreach ($items as $item) {
            if ($item instanceof Mage_Sales_Model_Quote_Address_Item) {
                $quoteItem = $item->getAddress()->getQuote()->getItemById($item->getQuoteItemId());
            } else {
                $quoteItem = $item;
            }
            if (!$quoteItem->getParentItem()) {
                $address->setTotalQty($address->getTotalQty() + $item->getQty());
            }
        }
        return $this;
    }

//    public function appliedToSimpleProduct(Varien_Event_Observer $observer)
//    {
//        /* @var $quote Mage_Sales_Model_Quote */
//        $quote = $observer->getEvent()->getQuote();
//        $reservedAttributes = array_flip($this->_reservedAttributes);
//        /* @var $item Mage_Sales_Model_Quote_Item */
//        foreach ($quote->getItemsCollection() as $item) {
//            if ($item->getProductType() == 'configurable' && $item->getHasChildren()) {
//                $child = $item->getChildren();
//                if (($child = reset($child)) && $childProduct = $child->getProduct()) {
//                    $product = $item->getProduct();
//                    foreach ($childProduct->getAttributes() as $k => $v) {
//                        if (isset($reservedAttributes[$k])) continue;
//                        $product->setData($k, $childProduct->getData($k));
//                    }
//                }
//            }
//        }
//    }

    public function updateRuleBehaviour(Varien_Event_Observer $observer)
    {
        if (!Mage::getStoreConfig(Gri_SalesRule_Helper_Data::CONFIG_PATH_ORIGINAL_PRICE)) return;
        /* @var $quote Mage_Sales_Model_Quote */
        $quote = $observer->getEvent()->getQuote();
        $address = $quote->getShippingAddress();
        $this->_prepareAddressTotals($address);
        $this->_calculator->reset($address);
        $store = Mage::app()->getStore($quote->getStoreId());
        $this->_calculator->init($store->getWebsiteId(), $quote->getCustomerGroupId(), $quote->getCouponCode());
        $items = $quote->getItemsCollection();
        /* @var $item Mage_Sales_Model_Quote_Item */
        foreach ($items as $item) {
            if ($item->getParentItemId()) {
                continue;
            }
            if ($this->_calculator->validate($item)) {
                $product = $item->getProduct();
                $product->setFinalPrice($product->getPrice())
                    ->setSkipResetFinalPrice(TRUE)
                    ->setFlashSalePriceCalculated(TRUE)
                    ->setSimplePricePriceCalculated(TRUE);
            }
        }
    }

    /**
     * Overrides Mage_SalesRule_Model_Observer::sales_order_afterPlace()
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function updateRuleUsage(Varien_Event_Observer $observer)
    {
        /* @var $invoice Mage_Sales_Model_Order_Invoice */
        $invoice = $observer->getEvent()->getInvoice();
        $order = $invoice->getOrder();

        if (!$order) {
            return $this;
        }

        // lookup rule ids
        $ruleIds = explode(',', $order->getAppliedRuleIds());
        $ruleIds = array_unique($ruleIds);

        $ruleCustomer = null;
        $customerId = $order->getCustomerId();

        // use each rule (and apply to customer, if applicable)
        foreach ($ruleIds as $ruleId) {
            if (!$ruleId) {
                continue;
            }
            $rule = Mage::getModel('salesrule/rule');
            $rule->load($ruleId);
            if ($rule->getId()) {
                $rule->setTimesUsed($rule->getTimesUsed() + 1);
                $rule->save();

                if ($customerId) {
                    $ruleCustomer = Mage::getModel('salesrule/rule_customer');
                    $ruleCustomer->loadByCustomerRule($customerId, $ruleId);

                    if ($ruleCustomer->getId()) {
                        $ruleCustomer->setTimesUsed($ruleCustomer->getTimesUsed()+1);
                    }
                    else {
                        $ruleCustomer
                            ->setCustomerId($customerId)
                            ->setRuleId($ruleId)
                            ->setTimesUsed(1);
                    }
                    $ruleCustomer->save();
                }
            }
        }

        $coupon = Mage::getModel('salesrule/coupon');
        /* @var Mage_SalesRule_Model_Coupon $coupon */
        $coupon->load($order->getCouponCode(), 'code');
        if ($coupon->getId()) {
            $coupon->setTimesUsed($coupon->getTimesUsed() + 1);
            $coupon->save();
            if ($customerId) {
                /* @var Mage_SalesRule_Model_Resource_Coupon_Usage $couponUsage */
                $couponUsage = Mage::getResourceModel('salesrule/coupon_usage');
                $couponUsage->updateCustomerCouponTimesUsed($customerId, $coupon->getId());
            }
        }
    }
}
