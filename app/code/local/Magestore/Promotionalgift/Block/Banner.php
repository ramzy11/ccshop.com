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
 * Promotionalgift Block
 * 
 * @category    Magestore
 * @package     Magestore_Promotionalgift
 * @author      Magestore Developer
 */
class Magestore_Promotionalgift_Block_Banner extends Mage_Core_Block_Template
{
    /**
     * prepare block's layout
     *
     * @return Magestore_Promotionalgift_Block_Shoppingcart
     */
    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }	
	
	public function getShoppingcartRule()
	{	
		$quote = Mage::getModel('checkout/session')->getQuote();	
		$rule = Mage::getModel('promotionalgift/shoppingcartrule')->validateQuote($quote);		
		if($rule != false)
			return $rule;		
		else
			return false;
	}
	
	public function getCouponCodeRule()
	{	
		$session = Mage::getModel('checkout/session');		
		$ruleId = $session->getData('promotionalgift_shoppingcart_rule_id');
		$ruleUsed = $session->getData('promotionalgift_shoppingcart_rule_used');		
		if($ruleUsed)
			return false;
		if($ruleId)
			$rule = Mage::getModel('promotionalgift/shoppingcartrule')->load($ruleId);		
		if($rule != false)
			return $rule;		
		else
			return false;
	}
	
	public function getRulePriorityLess($rule, $couponRule = false)
	{
		$ruleIds = array($rule->getId());	
		$rules = Mage::getModel('promotionalgift/shoppingcartrule')->getCollection()
										->addFieldToFilter('priority', array('gteq'=>$rule->getPriority(),																			
																			));
																			
		if($couponRule){
			$ruleIds = array_merge_recursive(array($rule->getId()),array($couponRule->getId()));
			$rules = Mage::getModel('promotionalgift/shoppingcartrule')->getCollection()
										->addFieldToFilter('priority', array('gteq'=>$rule->getPriority(),
																			'gteq'=>$couponRule->getPriority()
																			));
		}																					
		$ruleIds = array_merge_recursive($ruleIds, $rules->getAllIds());		
		$ruleIds = array_unique($ruleIds);				
		return $ruleIds;
	}
	
	public function getListShoppingcartRule()
	{	
		$couponRuleActived = $this->getCouponCodeRule();		
		if($couponRuleActived){
			$couponRuleId = $couponRuleActived->getId();			
		}
		
		$ruleActived = $this->getShoppingcartRule();		
		if($ruleActived){
			$ruleId = $ruleActived->getId();			
		}
		$shoppingcartRules = Mage::getModel('promotionalgift/shoppingcartrule')
										->getAvailableRule();
		if(isset($ruleId) && $ruleId > 0){		
			$rulePriorityLess = $this->getRulePriorityLess($ruleActived, $couponRuleActived);
			// Zend_debug::dump($rulePriorityLess);die();
			$shoppingcartRules = Mage::getModel('promotionalgift/shoppingcartrule')
										->getAvailableRule($rulePriorityLess);									
		}
		return $shoppingcartRules;
	}
	
	public function getRuleName($name)
	{
		if(strlen($name) >= 85){
			$name = substr($name,0,84);
			$name = $name.'...';
		}
		return $name;
	}
		
}