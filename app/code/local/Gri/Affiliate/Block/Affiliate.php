<?php

class Gri_Affiliate_Block_Affiliate extends Mage_Core_Block_Template
{

    protected function _construct()
    {
        parent::_construct();
    }

    /**
     * @return Gri_Affiliate_Model_Order
     */
    public function getAffiliateOrder()
    {
        if ($this->_getData('affiliate_order') === NULL) {
            /* @var $affiliateOrder Gri_Affiliate_Model_Order */
            $affiliateOrder = Mage::getModel('gri_affiliate/order');
            if (($order = $this->getOrder()) instanceof Mage_Sales_Model_Order) {
                $affiliateOrder->loadByOrder($order);
            }
            $this->setData('affiliate_order', $affiliateOrder);
        }
        return $this->_getData('affiliate_order');
    }

    /**
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return Mage::registry('current_order');
    }
}
