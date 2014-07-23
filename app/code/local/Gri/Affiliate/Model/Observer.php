<?php

class Gri_Affiliate_Model_Observer extends Varien_Object
{

    public function createAffiliateOrder(Varien_Event_Observer $observer)
    {
        /* @var $affiliate Gri_Affiliate_Model_Affiliate */
        $affiliate = Mage::getSingleton('gri_affiliate/affiliate');
        $affiliate->createAffiliateOrder($observer->getEvent()->getOrder());
    }

    public function identifyAffiliate(Varien_Event_Observer $observer)
    {
        /* @var $affiliate Gri_Affiliate_Model_Affiliate */
        $affiliate = Mage::getSingleton('gri_affiliate/affiliate');
        $affiliate->identify($observer->getEvent()->getFront()->getRequest());
    }
}
