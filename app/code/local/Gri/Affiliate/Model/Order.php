<?php

/**
 * Class Gri_Affiliate_Model_Order
 * @method string getAffiliate() Get Affiliate Code
 * @method string getHash() Get Affiliate Hash
 * @method integer getIsSent() Get is_sent Flag
 * @method Gri_Affiliate_Model_Resource_Order_Collection getCollection() Get Resource Collection Model
 * @method Gri_Affiliate_Model_Resource_Order getResource() Get Resource Model
 * @method Gri_Affiliate_Model_Order setIsSent(integer $isSent) Set is_sent Flag
 * @method Gri_Affiliate_Model_Order setOrderSuccessAt(string $time) Set Order Success Time
 * @method Gri_Affiliate_Model_Order setLandingPage() Set Affiliate Landing Page
 */
class Gri_Affiliate_Model_Order extends Mage_Core_Model_Abstract
{

    protected function _beforeSave()
    {
        $now = Varien_Date::now();
        $this->getCreatedAt() or $this->setCreatedAt($now);
        $this->setUpdatedAt($now);
        return parent::_beforeSave();
    }

    protected function _construct()
    {
        $this->_init('gri_affiliate/order');
    }

    public function getCountOfSentOrder()
    {
        return $this->getResource()->getCountOfSentOrder($this);
    }

    /**
     * @return Gri_Affiliate_Model_Affiliate_Abstract
     */
    public function getAffiliateInstance()
    {
        if ($this->_getData('affiliate_instance') === NULL) {
            $this->setAffiliateInstance($affiliate = $this->getResource()->getAffiliateModel($this->getAffiliate()));
            $affiliate->setAffiliateOrder($this);
        }
        return $this->_getData('affiliate_instance');
    }

    /**
     * Get Sales Order
     *
     * @return null|Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        if ($this->getOrderId() && $this->_getData('order') === NULL) {
            /* @var $order Mage_Sales_Model_Order */
            $order = Mage::getModel('sales/order')->load($this->getOrderId());
            $this->setOrder($order);
        }
        return $this->_getData('order');
    }

    public function loadByOrder(Mage_Sales_Model_Order $order)
    {
        if ($order->getId()) {
            $this->load($order->getId(), 'order_id');
            $this->setOrder($order);
        }
        return $this;
    }

    /**
     * @param Gri_Affiliate_Model_Affiliate_Abstract $affiliate
     * @return $this
     */
    public function setAffiliateInstance(Gri_Affiliate_Model_Affiliate_Abstract $affiliate)
    {
        $this->setData('affiliate_instance', $affiliate)
            ->setData('affiliate', $affiliate->getCode())
            ->setHash($affiliate->getHash());
        return $this;
    }

    /**
     * @param Mage_Sales_Model_Order $order
     * @return $this
     */
    public function setOrder(Mage_Sales_Model_Order $order)
    {
        $this->setData('order', $order)
            ->setData('order_id', $order->getId());
        return $this;
    }

    public function updateIsSent($isSent)
    {
        $isSent = (int)$isSent;
        if ($this->getIsSent() != $isSent) {
            $this->setIsSent($isSent)
                ->setOrderSuccessAt(Varien_Date::now())
                ->save();
        }
    }
}
