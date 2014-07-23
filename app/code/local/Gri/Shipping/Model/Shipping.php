<?php

class Gri_Shipping_Model_Shipping extends Mage_Shipping_Model_Shipping
{
    protected $_checkout;

    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        if ($request->getDestCityCode() === NULL) {
            if (is_array($items = $request->getAllItems())) {
                /* @var $item Mage_Sales_Model_Quote_Item_Abstract */
                $item = reset($items);
                if ($item instanceof Mage_Sales_Model_Quote_Item &&
                    ($address = $item->getQuote()->getShippingAddress()) &&
                    $address->getCityId()
                ) {
                    /* @var $city Gri_Directory_Model_City */
                    $city = Mage::getModel('gri_directory/city');
                    $city->load($address->getCityId());
                    $request->setDestCityCode($city->getCode() ? $city->getCode() : '');
                }
            }
        }
        parent::collectRates($request);
        $this->upgradeShippingMethod();
        return $this;
    }

    /**
     * @return Gri_Shipping_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('gri_shipping');
    }

    /**
     * Get checkout session
     *
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckout()
    {
        if (NULL === $this->_checkout) {
            $this->_checkout = Mage::getSingleton('checkout/session');
        }
        return $this->_checkout;
    }

    public function upgradeShippingMethod()
    {
        $ruleIds = explode(',', $this->getCheckout()->getQuote()->getAppliedRuleIds());
        $ruleIds = array_unique($ruleIds);
        /* @var $rule Mage_SalesRule_Model_Rule */
        $rule = Mage::getModel('salesrule/rule');

        // Check free upgrade shipping method action
        foreach ($ruleIds as $ruleId) {
            if (!$ruleId) {
                continue;
            }
            $rule->load($ruleId);
            if ($rule->getId() && $rule->getIsActive() &&
                $rule->getSimpleAction() == Gri_Sales_Helper_Data::UPGRADE_SHIPPING_METHOD
            ) {
                $minPrice = 0;
                // Get minimum shipping amount
                /* @var $rate Mage_Shipping_Model_Rate_Result_Method */
                foreach ($rates = $this->getResult()->getAllRates() as $rate) {
                    $price = $rate->getPrice();
                    $price > 0 && ($price < $minPrice || $minPrice == 0) and
                        $minPrice = $price;
                }

                // Apply minimum shipping amount
                foreach ($rates as $rate) {
                    $rate->getPrice() > $minPrice and $rate->setPrice($minPrice)
                        ->setMethodTitle($rate->getMethodTitle() .
                            $this->_getHelper()->__(' (Free Upgraded)')
                        )
                    ;
                }
                break;
            }
        }
        return $this;
    }
}
