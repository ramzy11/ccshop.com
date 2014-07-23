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
 * Promotionalgift Helper
 * 
 * @category    Magestore
 * @package     Magestore_Promotionalgift
 * @author      Magestore Developer
 */
class Magestore_Promotionalgift_Helper_Data extends Mage_Core_Helper_Abstract
{
    
	public function getShoppingcartFreeGifts($ruleId)
	{		
		$freeGifts = Mage::getModel('promotionalgift/shoppingcartitem')
									->load($ruleId, 'rule_id');
		$productIds = explode(',', $freeGifts->getProductIds());
		$qtyItems = explode(',', $freeGifts->getGiftQty());
		$i = 0;
		$giftitems = array();
		if($freeGifts->getProductIds()){
			foreach($productIds as $productId){
				$giftitems[] = array('gift_qty' => $qtyItems[$i],
									 'product_id' => $productId,
									);	
				$i++;
			}
		}
    	return $giftitems;
	}
	
	public function getPromotionalgiftUrl()
	{
		$url = $this->_getUrl('promotionalgift/index/index', array());		
		return $url;
	}	
	
	public function getStoreId()
	{
		return Mage::app()->getStore()->getId();
	}
	
	public function enablePromotionalgift()
	{
        if (!Mage::helper('magenotification')->checkLicenseKey('Promotionalgift')) {return false;}
		return Mage::getStoreConfig('promotionalgift/general/enable', $this->getStoreId());
	}
	
	public function getPromotionalIcon()
	{
		return Mage::getStoreConfig('promotionalgift/general/giftlabel', $this->getStoreId());
	}	
	
	public function showFreeGift()
	{
		return Mage::getStoreConfig('promotionalgift/general/showfreegift', $this->getStoreId());
	}
	
	public function getReportConfig($code,$store = null){
		return Mage::getStoreConfig('promotionalgift/report/'.$code,$store);
	}
	
	public function getShoppingcartRule()
	{	
		$session = Mage::getModel('checkout/session');			
		$quote = Mage::getModel('checkout/session')->getQuote();	
		$rule = Mage::getModel('promotionalgift/shoppingcartrule')->validateQuote($quote);		
		if($rule){
			return $rule;
		}else{
			return false;
		}
	}
	
	/**
     * get Mini cart block class
     * 
     * @return string
     */
    public function getMiniCartClass() {
        if (!isset($this->_cache['mini_cart_class'])) {
            $minicartSelect = '';
            if ($minicartBlock = Mage::app()->getLayout()->getBlock('cart_sidebar')) {
                $xmlMinicart = simplexml_load_string($this->toXMLElement($minicartBlock->toHtml()));
                $attributes = $xmlMinicart->attributes();
                if ($id = (string)$attributes->id) {
                    $minicartSelect = "#$id";
                } elseif ($class = (string)$attributes->class) {
                    $minicartSelect = '[class="' . $class . '"]';
                }
            }
            $this->_cache['mini_cart_class'] = $minicartSelect;
        }
        return $this->_cache['mini_cart_class'];
    }
	
	public function toXMLElement($html) {
        $open = trim(substr($html, 0, strpos($html, '>')+1));
        $close = '</' . substr($open, 1, strpos($open, ' ')-1) . '>';
        if ($xml = $open . $close) {
            return $xml;
        }
        return '<div></div>';
    }
	
	public function deleteGiftItemOfRule($ruleId = null)
	{
		$session = Mage::getModel('checkout/session');
		$shoppingcartRuleId = $session->getData('promotionalgift_shoppingcart_rule_id');	
		$quote = $session->getQuote();
		$quoteId = $quote->getId();
		$cart = Mage::getModel('checkout/cart');
		$shoppingQuotes = Mage::getModel('promotionalgift/shoppingquote')->getCollection()
															->addFieldToFilter('quote_id', $quoteId)														
															;
        if($ruleId)	
			$shoppingQuotes = $shoppingQuotes->addFieldToFilter('shoppingcartrule_id', array('nin'=>$ruleId));
		if($shoppingcartRuleId){	
			$rule = Mage::getModel('promotionalgift/shoppingcartrule')->load($shoppingcartRuleId);
			if(!$this->validateRuleQuote($rule)){			
				$session->setData('promotionalgift_shoppingcart_rule_id', null);
				$session->setData('shoppingcart_couponcode_rule_id', null);	
				$session->setData('promotionalgift_shoppingcart_rule_used', null);	
				$session->setData('promptionalgift_coupon_code', null);	
			}else{
				$shoppingQuotes = $shoppingQuotes->addFieldToFilter('shoppingcartrule_id', array('nin'=>$shoppingcartRuleId));	
			}
		}
		$change = 0;
		if(count($shoppingQuotes)){	
			foreach($shoppingQuotes as $shoppingQuote){	
				try{		
					$item = $cart->getQuote()->getItemById($shoppingQuote->getItemId());
					if($item){
						$item->delete();
						$change = 1;
					}
					$shoppingQuote->delete();
					$i++;					
				}catch(Exception $e){					
				}
			}
			if($change==1){
				if($this->enablePromotionalgift()){
					$url = Mage::getUrl('checkout/cart/index');
					if(Mage::getModel('checkout/session')->getData('back_url_promotionalgift')){
						$url = Mage::getModel('checkout/session')->getData('back_url_promotionalgift');
					}
					header('Location:'.$url);
					exit;
				}
			}
		}	
	}
	
	public function checkCartRule()
	{		
		$sRule = $this->getShoppingcartRule();				
		if(!$sRule){				
			$this->deleteGiftItemOfRule();				
		}elseif($sRule->getId()){	
			$this->deleteGiftItemOfRule($sRule->getId());								
		}			
	}
	
	public function validateRuleQuote($availableRule) {
		$quote = Mage::getModel('checkout/session')->getQuote();
		if ($quote->isVirtual()) {
            $address = $quote->getBillingAddress();
        } else {
            $address = $quote->getShippingAddress();
        }        
		$availableRule->afterLoad();
		if($availableRule->validate($address))
			return $availableRule;			
		return false;		
    }
}