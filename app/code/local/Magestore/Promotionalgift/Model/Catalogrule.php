<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_Promotionalgift
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Promotionalgift Model
 * 
 * @category    Magestore
 * @package     Magestore_Promotionalgift
 * @author      Magestore Developer
 */
class Magestore_Promotionalgift_Model_Catalogrule extends Mage_Rule_Model_Rule
{	
    public function _construct()
    {
        parent::_construct();
        $this->_init('promotionalgift/catalogrule');
		$this->setIdFieldName('rule_id');
    }
	
	public function getConditionsInstance()
	{
    	return Mage::getModel('promotionalgift/catalogrule_condition_combine');
    }
	
	public function validateItem($productId) {
        $availableRules = $this->getAvailableRule();
		if(!$availableRules) return false;
		$product = Mage::getModel('catalog/product')->load($productId);
		foreach($availableRules as $availableRule){
			$availableRule->afterLoad();
			if($availableRule->validate($product))
				return $availableRule;
		}
		return false;
    }
	
	public function getAvailableRule() 
	{
		// if(!Mage::helper('promotionalgift')->checkModuleEnable()) return null;
		$availableRules = $this->getCollection();
		$availableRules->addFieldToFilter('website_ids',array('finset' => Mage::app()->getStore()->getWebsiteId()));
		$availableRules->addFieldToFilter('status','1');
		if(Mage::getModel('customer/session')->isLoggedIn()){
			$customer = Mage::getModel('customer/customer')->load(Mage::getModel('customer/session')->getCustomerId());
			$availableRules->addFieldToFilter('customer_group_ids',array('finset' => $customer->getGroupId()));
		}else{
			$availableRules->addFieldToFilter('customer_group_ids',array('finset' => Mage_Customer_Model_Group::NOT_LOGGED_IN_ID));
		}
		$availableRules->getSelect()->where('(from_date IS NULL) OR (date(from_date) <= date(?))',date("Y-m-d",strtotime(now())));
		$availableRules->getSelect()->where('(to_date IS NULL) OR (date(to_date) >= date(?))',date("Y-m-d",strtotime(now())));
		$availableRules->getSelect()->where('(uses_limit IS NULL) OR (uses_limit > 0)');
		$availableRules->setOrder('priority','ASC');
		if(count($availableRules))
			return $availableRules;
		return null;
    }
	
	/**
     * Fix error when load and save with collection
     */
    protected function _afterLoad()
    {
        $this->setConditions(null);
        $this->setActions(null);
        return parent::_afterLoad();
    }
	
	/**
     * Fix bug when save website ids and customer group id in magento v1.7
     * 
     **/   
    protected function _beforeSave()
    {
        parent::_beforeSave();
        if ($this->hasWebsiteIds()) {
            $websiteIds = $this->getWebsiteIds();
            if (is_array($websiteIds) && !empty($websiteIds)) {
                $this->setWebsiteIds(implode(',', $websiteIds));
            }
        }
        if ($this->hasCustomerGroupIds()) {
            $groupIds = $this->getCustomerGroupIds();
            if (is_array($groupIds) && !empty($groupIds)) {
                $this->setCustomerGroupIds(implode(',', $groupIds));
            }
        }
        return $this;
    }
}