<?php

class Gri_Affiliate_Model_Affiliate extends Varien_Object
{
    const CONFIG_PATH_IDENTICAL_PARAM = 'gri_affiliate/general/identical_param';

    public function createAffiliateOrder(Mage_Sales_Model_Order $order)
    {
        if ($affiliate = $this->getCurrentAffiliate()) {
            /* @var $affiliateOrder Gri_Affiliate_Model_Order */
            $affiliateOrder = Mage::getModel('gri_affiliate/order');
            $affiliateOrder->setOrder($order)
                ->setAffiliateInstance($affiliate)
                ->setLandingPage($affiliate->getLandingPage());
            if ($order->getId() &&
                $affiliate->isEnabled() &&
                $affiliate->isInDate() &&
                (!$affiliate->getSentLimit() || $affiliateOrder->getCountOfSentOrder() < $affiliate->getSentLimit())
            ) $affiliateOrder->save();
        }
    }

    /**
     * @return Gri_Affiliate_Model_Resource_Order
     */
    public function getAffiliateOrderResource()
    {
        return Mage::getResourceSingleton('gri_affiliate/order');
    }

    /**
     * @return false|Gri_Affiliate_Model_Affiliate_Abstract
     */
    public function getCurrentAffiliate()
    {
        if ($this->_getData('current_affiliate') === NULL) {
            if (($code = Mage::app()->getCookie()->get(Gri_Affiliate_Model_Affiliate_Abstract::COOKIE_PATH_AFFILIATE)) &&
                ($affiliate = $this->getAffiliateOrderResource()->getAffiliateModel($code)) &&
                $affiliate->isEnabled()
            ) {
                $this->setData('current_affiliate', $affiliate);
            }
            else $this->setData('current_affiliate', FALSE);
        }
        return $this->_getData('current_affiliate');
    }

    public function identify(Mage_Core_Controller_Request_Http $request)
    {
        if (($code = $request->getParam($paramName = Mage::getStoreConfig(self::CONFIG_PATH_IDENTICAL_PARAM))) &&
            ($affiliate = $this->getAffiliateOrderResource()->getAffiliateModel($code)) &&
            $affiliate->isEnabled()
        ) {
            $affiliate->identify($paramName);
        }
    }
}
