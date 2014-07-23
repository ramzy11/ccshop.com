<?php

/**
 * Class Gri_SalesRule_Model_Validator
 * @method array getRevertedTypes()
 * @method Gri_SalesRule_Model_Validator setRevertedTypes(array $types)
 */
class Gri_SalesRule_Model_Validator extends Mage_SalesRule_Model_Validator
{
    protected $_reservedAttributes = array(
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
    );

    protected function _construct()
    {
        parent::_construct();
        $this->setRevertedTypes(array(
            Amasty_Rules_Helper_Data::TYPE_XY_PERCENT,
            Amasty_Rules_Helper_Data::TYPE_XY_FIXED,
        ));
    }

    protected function _canProcessRule($rule, $address)
    {
//        $checkScope = (!$rule->hasIsValidForAddress($address) || $address->isObjectNew()) &&
//        $rule->getConditionScope() == 'simple';

        $checkScope = $this->_checkScope($rule);
        if ($checkScope) {
            foreach ($address->getQuote()->getItemsCollection() as $item) {
                $this->_processConditionScope($item);
            }
        }
        $result = parent::_canProcessRule($rule, $address);
        if ($checkScope) {
            foreach ($address->getQuote()->getItemsCollection() as $item) {
                $this->_postProcessConditionScope($item);
            }
        }
        return $result;
    }

    /**
     * @param $rule
     * @return bool
     */
    protected function _checkScope($rule)
    {
        return $rule->getConditionScope() == 'simple';
    }


    /**
     * @param Mage_Sales_Model_Quote_Item_Abstract $item
     * @return Gri_SalesRule_Model_Validator
     */
    protected function _postProcessConditionScope($item)
    {
        if ($item->getProductType() == 'configurable' && $item->getHasChildren()) {
            is_array($oldScopeData = $item->getOldScopeData()) and $item->getProduct()->addData($oldScopeData);
        }
        return $this;
    }

    /**
     * @param Mage_Sales_Model_Quote_Item_Abstract $item
     * @return Gri_SalesRule_Model_Validator
     */
    protected function _processConditionScope($item)
    {
        if ($item->getProductType() == 'configurable' && $item->getHasChildren()) {
            $product = $item->getProduct();
            if (!is_array($simpleScopeData = $item->getSimpleScopeData())) {
                $reservedAttributes = array_flip($this->_reservedAttributes);
                $simpleScopeData = array();
                $oldScopeData = array();
                $child = $item->getChildren();
                if (($child = reset($child)) && $childProduct = $child->getProduct()) {
                    foreach ($childProduct->getAttributes() as $k => $v) {
                        if (isset($reservedAttributes[$k])) continue;
                        $simpleScopeData[$k] = $childProduct->getData($k);
                        $oldScopeData[$k] = $product->getData($k);
                    }
                }
                $item->setSimpleScopeData($simpleScopeData)
                    ->setOldScopeData($oldScopeData);
            }
            $product->addData($simpleScopeData);
        }
        return $this;
    }

    public function validate(Mage_Sales_Model_Quote_Item_Abstract $item)
    {
        $address = $this->_getAddress($item);
        $revertedTypes = $this->getRevertedTypes();
        /* @var $rule Mage_SalesRule_Model_Rule */
        foreach ($this->_getRules() as $rule) {
            if ($rule->getSimpleFreeShipping() && !($rule->getDiscountAmount() * 1)) continue;
            $result = $this->_canProcessRule($rule, $address) && $rule->getActions()->validate($item);
            // Reverted types: Items that meet the conditions won't be discounted
            if (in_array($rule->getSimpleAction(), $revertedTypes)) {
                if ($result) {
                    continue;
                } else {
                    if ($rule->getPromoSku() || $rule->getPromoCats()) {
                        $skus = explode(',', $rule->getPromoSku());
                        $cats = explode(',', $rule->getPromoCats());
                        if (!$product = $item->getRealProcut()) {
                            $product = Mage::getModel('catalog/product')
                                ->setStoreId($item->getQuote()->getStoreId())
                                ->load($item->getProductId());
                            $item->setRealProduct($product);
                        }
                        if (in_array($product->getSku(), $skus) || array_intersect($cats, $product->getCategoryIds())) {
                            return TRUE;
                        }
                    }
                }
            } else if ($result) {
                return TRUE;
            }
        }
        return FALSE;
    }


    /**
     * Quote item discount calculation process
     *
     * @param   Gri_Sales_Model_Quote_Item_Abstract $item
     * @return  Gri_SalesRule_Model_Validator
     */
    public function process(Mage_Sales_Model_Quote_Item_Abstract $item)
    {
        $item->setDiscountAmount(0);
        $item->setBaseDiscountAmount(0);
        $item->setDiscountPercent(0);
        $quote      = $item->getQuote();
        $address    = $this->_getAddress($item);

        $itemPrice              = $this->_getItemPrice($item);
        $baseItemPrice          = $this->_getItemBasePrice($item);
        $itemOriginalPrice      = $this->_getItemOriginalPrice($item);
        $baseItemOriginalPrice  = $this->_getItemBaseOriginalPrice($item);

        if ($itemPrice < 0) {
            return $this;
        }

        $appliedRuleIds = array();
        foreach ($this->_getRules() as $rule) {
            /* @var $rule Mage_SalesRule_Model_Rule */
            if (!$this->_canProcessRule($rule, $address)) {
                continue;
            }

            /**
             *  Fro Configurable Product ,color_filter_1 Or color_filter_2  isn't owned by Configurable product
             */
           if($this->_checkScope($rule)){
                $this->_processConditionScope($item);
           }

            if (!$rule->getActions()->validate($item)) {
                continue;
            }

            $qty = $this->_getItemQty($item, $rule);
            $rulePercent = min(100, $rule->getDiscountAmount());

            $discountAmount = 0;
            $baseDiscountAmount = 0;
            //discount for original price
            $originalDiscountAmount = 0;
            $baseOriginalDiscountAmount = 0;

            switch ($rule->getSimpleAction()) {
                case Mage_SalesRule_Model_Rule::TO_PERCENT_ACTION:
                    $rulePercent = max(0, 100-$rule->getDiscountAmount());
                //no break;
                case Mage_SalesRule_Model_Rule::BY_PERCENT_ACTION:
                    $step = $rule->getDiscountStep();
                    if ($step) {
                        $qty = floor($qty/$step)*$step;
                    }
                    $_rulePct = $rulePercent/100;
                    $discountAmount    = ($qty*$itemPrice - $item->getDiscountAmount()) * $_rulePct;
                    $baseDiscountAmount= ($qty*$baseItemPrice - $item->getBaseDiscountAmount()) * $_rulePct;
                    //get discount for original price
                    $originalDiscountAmount    = ($qty*$itemOriginalPrice - $item->getDiscountAmount()) * $_rulePct;
                    $baseOriginalDiscountAmount= ($qty*$baseItemOriginalPrice - $item->getDiscountAmount()) * $_rulePct;

                    if (!$rule->getDiscountQty() || $rule->getDiscountQty()>$qty) {
                        $discountPercent = min(100, $item->getDiscountPercent()+$rulePercent);
                        $item->setDiscountPercent($discountPercent);
                    }
                    break;
                case Mage_SalesRule_Model_Rule::TO_FIXED_ACTION:
                    $quoteAmount = $quote->getStore()->convertPrice($rule->getDiscountAmount());
                    $discountAmount    = $qty*($itemPrice-$quoteAmount);
                    $baseDiscountAmount= $qty*($baseItemPrice-$rule->getDiscountAmount());
                    //get discount for original price
                    $originalDiscountAmount    = $qty*($itemOriginalPrice-$quoteAmount);
                    $baseOriginalDiscountAmount= $qty*($baseItemOriginalPrice-$rule->getDiscountAmount());
                    break;

                case Mage_SalesRule_Model_Rule::BY_FIXED_ACTION:
                    $step = $rule->getDiscountStep();
                    if ($step) {
                        $qty = floor($qty/$step)*$step;
                    }
                    $quoteAmount        = $quote->getStore()->convertPrice($rule->getDiscountAmount());
                    $discountAmount     = $qty*$quoteAmount;
                    $baseDiscountAmount = $qty*$rule->getDiscountAmount();
                    break;

                case Mage_SalesRule_Model_Rule::CART_FIXED_ACTION:
                    if (empty($this->_rulesItemTotals[$rule->getId()])) {
                        Mage::throwException(Mage::helper('salesrule')->__('Item totals are not set for rule.'));
                    }

                    /**
                     * prevent applying whole cart discount for every shipping order, but only for first order
                     */
                    if ($quote->getIsMultiShipping()) {
                        $usedForAddressId = $this->getCartFixedRuleUsedForAddress($rule->getId());
                        if ($usedForAddressId && $usedForAddressId != $address->getId()) {
                            break;
                        } else {
                            $this->setCartFixedRuleUsedForAddress($rule->getId(), $address->getId());
                        }
                    }
                    $cartRules = $address->getCartFixedRules();
                    if (!isset($cartRules[$rule->getId()])) {
                        $cartRules[$rule->getId()] = $rule->getDiscountAmount();
                    }

                    if ($cartRules[$rule->getId()] > 0) {
                        if ($this->_rulesItemTotals[$rule->getId()]['items_count'] <= 1) {
                            $quoteAmount = $quote->getStore()->convertPrice($cartRules[$rule->getId()]);
                            $baseDiscountAmount = min($baseItemPrice * $qty, $cartRules[$rule->getId()]);
                        } else {
                            $discountRate = $baseItemPrice * $qty /
                                $this->_rulesItemTotals[$rule->getId()]['base_items_price'];
                            $maximumItemDiscount = $rule->getDiscountAmount() * $discountRate;
                            $quoteAmount = $quote->getStore()->convertPrice($maximumItemDiscount);

                            $baseDiscountAmount = min($baseItemPrice * $qty, $maximumItemDiscount);
                            $this->_rulesItemTotals[$rule->getId()]['items_count']--;
                        }

                        $discountAmount = min($itemPrice * $qty, $quoteAmount);
                        $discountAmount = $quote->getStore()->roundPrice($discountAmount);
                        $baseDiscountAmount = $quote->getStore()->roundPrice($baseDiscountAmount);

                        //get discount for original price
                        $originalDiscountAmount = min($itemOriginalPrice * $qty, $quoteAmount);
                        $baseOriginalDiscountAmount = $quote->getStore()->roundPrice($originalDiscountAmount);
                        $baseOriginalDiscountAmount = $quote->getStore()->roundPrice($baseItemOriginalPrice);

                        $cartRules[$rule->getId()] -= $baseDiscountAmount;
                    }
                    $address->setCartFixedRules($cartRules);

                    break;

                case Mage_SalesRule_Model_Rule::BUY_X_GET_Y_ACTION:
                    $x = $rule->getDiscountStep();
                    $y = $rule->getDiscountAmount();
                    if (!$x || $y > $x) {
                        break;
                    }
                    $buyAndDiscountQty = $x + $y;

                    $fullRuleQtyPeriod = floor($qty / $buyAndDiscountQty);
                    $freeQty  = $qty - $fullRuleQtyPeriod * $buyAndDiscountQty;

                    $discountQty = $fullRuleQtyPeriod * $y;
                    if ($freeQty > $x) {
                        $discountQty += $freeQty - $x;
                    }

                    $discountAmount    = $discountQty * $itemPrice;
                    $baseDiscountAmount= $discountQty * $baseItemPrice;
                    //get discount for original price
                    $originalDiscountAmount    = $discountQty * $itemOriginalPrice;
                    $baseOriginalDiscountAmount= $discountQty * $baseItemOriginalPrice;
                    break;
            }

            $result = new Varien_Object(array(
                'discount_amount'      => $discountAmount,
                'base_discount_amount' => $baseDiscountAmount,
            ));
            Mage::dispatchEvent('salesrule_validator_process', array(
                'rule'    => $rule,
                'item'    => $item,
                'address' => $address,
                'quote'   => $quote,
                'qty'     => $qty,
                'result'  => $result,
            ));

            $discountAmount = $result->getDiscountAmount();
            $baseDiscountAmount = $result->getBaseDiscountAmount();

            $percentKey = $item->getDiscountPercent();
            /**
             * Process "delta" rounding
             */
            if ($percentKey) {
                $delta      = isset($this->_roundingDeltas[$percentKey]) ? $this->_roundingDeltas[$percentKey] : 0;
                $baseDelta  = isset($this->_baseRoundingDeltas[$percentKey])
                    ? $this->_baseRoundingDeltas[$percentKey]
                    : 0;
                $discountAmount+= $delta;
                $baseDiscountAmount+=$baseDelta;

                $this->_roundingDeltas[$percentKey]     = $discountAmount -
                    $quote->getStore()->roundPrice($discountAmount);
                $this->_baseRoundingDeltas[$percentKey] = $baseDiscountAmount -
                    $quote->getStore()->roundPrice($baseDiscountAmount);
                $discountAmount = $quote->getStore()->roundPrice($discountAmount);
                $baseDiscountAmount = $quote->getStore()->roundPrice($baseDiscountAmount);
            } else {
                $discountAmount     = $quote->getStore()->roundPrice($discountAmount);
                $baseDiscountAmount = $quote->getStore()->roundPrice($baseDiscountAmount);
            }

            /**
             * We can't use row total here because row total not include tax
             * Discount can be applied on price included tax
             */

            $itemDiscountAmount = $item->getDiscountAmount();
            $itemBaseDiscountAmount = $item->getBaseDiscountAmount();

            $discountAmount     = min($itemDiscountAmount + $discountAmount, $itemPrice * $qty);
            $baseDiscountAmount = min($itemBaseDiscountAmount + $baseDiscountAmount, $baseItemPrice * $qty);

            $item->setDiscountAmount($discountAmount);
            $item->setBaseDiscountAmount($baseDiscountAmount);

            $item->setOriginalDiscountAmount($originalDiscountAmount);
            $item->setBaseOriginalDiscountAmount($baseOriginalDiscountAmount);

            $appliedRuleIds[$rule->getRuleId()] = $rule->getRuleId();

            $this->_maintainAddressCouponCode($address, $rule);
            $this->_addDiscountDescription($address, $rule);

            if ($rule->getStopRulesProcessing()) {
                break;
            }
        }

        $item->setAppliedRuleIds(join(',',$appliedRuleIds));
        $address->setAppliedRuleIds($this->mergeIds($address->getAppliedRuleIds(), $appliedRuleIds));
        $quote->setAppliedRuleIds($this->mergeIds($quote->getAppliedRuleIds(), $appliedRuleIds));

        return $this;
    }
}
