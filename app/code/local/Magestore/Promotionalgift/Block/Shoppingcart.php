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
class Magestore_Promotionalgift_Block_Shoppingcart extends Mage_Core_Block_Template
{
    /**
     * prepare block's layout
     *
     * @return Magestore_Promotionalgift_Block_Shoppingcart
     */
    public function _prepareLayout()
    {
		$quote = Mage::getModel('checkout/session')->getQuote();
		//Check shoppingcart rule when update cart
		$shoppingQuotes = Mage::getModel('promotionalgift/shoppingquote')->getCollection()
														->addFieldToFilter('quote_id', $quote->getId());		
		if(count($shoppingQuotes))
			$helper = Mage::helper('promotionalgift')->checkCartRule();	
        return parent::_prepareLayout();
    }
	
	public function getShoppingcartRule()
	{	
		$session = Mage::getModel('checkout/session');	
		$quote = Mage::getModel('checkout/session')->getQuote();	
		$rule = Mage::getModel('promotionalgift/shoppingcartrule')->validateQuote($quote);
		$quoteId = Mage::getModel('checkout/session')->getQuote()->getId();
		if($rule != false){
			$ruleUsed = Mage::getModel('promotionalgift/shoppingquote')
							->getCollection()
							->addFieldToFilter('shoppingcartrule_id',$rule->getId())
							->addFieldToFilter('quote_id',$quoteId)
							->getFirstItem();
			if(!$ruleUsed->getId())
				return $rule;		
		}
		else
			return false;
	}
	
	public function getCouponCodeRule()
	{	
		$session = Mage::getModel('checkout/session');		
		$ruleId = $session->getData('promotionalgift_shoppingcart_rule_id');
		$ruleUsed = $session->getData('promotionalgift_shoppingcart_rule_used');
		// Zend_debug::dump($ruleUsed);die();
		if($ruleUsed)
			return false;
		if($ruleId)
			$rule = Mage::getModel('promotionalgift/shoppingcartrule')->load($ruleId);		
		if($rule != false)
			return $rule;		
		else
			return false;
	}
	
	public function getTotalItem($rule)
	{		
		$item = array();
		if($rule != false){
		$ruleItems = Mage::getModel('promotionalgift/shoppingcartitem')->load($rule->getId(),'rule_id')
																	  ->getProductIds();
            $items = explode(',', $ruleItems);
			$totalItems = count($items);
			return $totalItems;
        }						
		return false;
	}
	
	/* public function getTotalCouponItem()
	{
		$rule = $this->getCouponCodeRule();
		$item = array();
		if($rule != false){
		$ruleItems = Mage::getModel('promotionalgift/shoppingcartitem')->load($rule->getId(),'rule_id')
																	  ->getProductIds();
            $items = explode(',', $ruleItems);
			$totalItems = count($items);
			return $totalItems;
        }						
		return false;
	}
	
	public function getTotalItem()
	{
		return $this->getTotalCouponItem() + $this->getTotalQuoteItem();
	} */
	
	
	
	public function getListShoppingcartRule()
	{	
		$ruleActived = $this->getShoppingcartRule();		
		if($ruleActived){
			$ruleId = $ruleActived->getId();
			$priority = $ruleActived->getPriority();
		}
		$shoppingcartRules = Mage::getModel('promotionalgift/shoppingcartrule')
										->getAvailableRule();
		if(isset($ruleId) && $ruleId > 0){
			$rulePriorityLess = $this->getRulePriorityLess($ruleActived);
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
	
	public function getQtyProductRule($product, $rule)
	{	
		if($rule != false){
			$giftitems = Mage::helper('promotionalgift')->getShoppingcartFreeGifts($rule->getId());
			// Zend_debug::dump($giftitems);die('32');
			foreach($giftitems as $giftitem){
				if($giftitem['product_id'] == $product->getId())
					return $giftitem['gift_qty'];					
			}
		}
		return false;
	}
	
	public function getItemIds()
	{
		$quoteId = Mage::getModel('checkout/session')->getQuote()->getId();
		$itemIds = array();
		if($quoteId){
			$giftItems = Mage::getModel('promotionalgift/shoppingquote')->getCollection()
										        ->addFieldToFilter('quote_id', $quoteId);			
			foreach($giftItems as $item){
				$itemIds[] = $item->getItemId();
			}
			
			$productGifts = Mage::getModel('promotionalgift/quote')->getCollection()
										        ->addFieldToFilter('quote_id', $quoteId);
			foreach($productGifts as $product){
				$itemIds[] = $product->getItemId();
			}
			return implode(',', $itemIds);
		}
	}
	
	public function getEidtItemIds()
	{
		$quote = Mage::getModel('checkout/session')->getQuote();
		$quoteId = $quote->getId();
		$itemIds = array();
		if($quoteId){
			$giftItems = Mage::getModel('promotionalgift/shoppingquote')->getCollection()
										        ->addFieldToFilter('quote_id', $quoteId);			
			foreach($giftItems as $item){
				if(!$this->checkItemOption($item->getItemId()))
					$itemIds[] = $item->getItemId();
			}
			
			$productGifts = Mage::getModel('promotionalgift/quote')->getCollection()
										        ->addFieldToFilter('quote_id', $quoteId);
			foreach($productGifts as $product){
				if(!$this->checkItemOption($product->getItemId()))
					$itemIds[] = $product->getItemId();
			}
			return implode(',', $itemIds);
		}
	}
	
	public function checkItemOption($itemId)
	{		
		$item = Mage::getModel('sales/quote_item')->load($itemId);		
		$productId = $item->getProductId();
		$product = Mage::getModel('catalog/product')->load($productId);
		if($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_BUNDLE
		   ||$product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE
		   ||$product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_GROUPED
		   ||$product->getOptions()
		   ){
			return true;
		}
		return false;
	}
	
	public function getEidtItemOptionIds()
	{
		$quote = Mage::getModel('checkout/session')->getQuote();
		$quoteId = $quote->getId();
		$itemIds = array();
		if($quoteId){
			$giftItems = Mage::getModel('promotionalgift/shoppingquote')->getCollection()
										        ->addFieldToFilter('quote_id', $quoteId);			
			foreach($giftItems as $item){				
				$itemIds[] = $item->getItemId();
			}
			
			$productGifts = Mage::getModel('promotionalgift/quote')->getCollection()
										        ->addFieldToFilter('quote_id', $quoteId);
			foreach($productGifts as $product){				
				$itemIds[] = $product->getItemId();
			}
			return implode(',', $itemIds);
		}
	}
	
}