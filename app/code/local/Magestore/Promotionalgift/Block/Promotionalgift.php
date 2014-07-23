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
class Magestore_Promotionalgift_Block_Promotionalgift extends Mage_Catalog_Block_Product_List
{
    /**
     * prepare block's layout
     *
     * @return Magestore_Promotionalgift_Block_Promotionalgift
     */
    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }	
	
	protected function getProductIds()
	{
		$products = Mage::getResourceModel('catalog/product_collection');
		$productIds = array();
		foreach($products as $product){
			$availableRule = Mage::getModel('promotionalgift/catalogrule')->validateItem($product->getId());
			if($availableRule){
				$productIds[] = $product->getId();
			}
		}
		return $productIds;
	}
	
	protected function _getProductCollection()
    {	
		$this->_productCollection = Mage::getSingleton('promotionalgift/layer')->getProductCollection();
		// if (is_null($this->_productCollection)) {
			// $this->_productCollection  = Mage::getResourceModel('catalog/product_collection')
								// ->addAttributeToSelect('*')
								// ->addFieldToFilter('entity_id', array('in'=>$this->getProductIds()))
								// ->addMinimalPrice()		 
								// ->addTaxPercents()
								// ->addStoreFilter()
								// ;								
            // Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($this->_productCollection);
            // Mage::getSingleton('catalog/product_visibility')->addVisibleInSearchFilterToCollection($this->_productCollection);
		// }
		return $this->_productCollection;	
    }
		
	public function getListShoppingcartRule()
	{
		$shoppingcartRules = Mage::getModel('promotionalgift/shoppingcartrule')->getAvailableRule();
		return $shoppingcartRules;
	}
	
	public function getRuleByPruduct($productId)
	{
		$catalogRule = Mage::getModel('promotionalgift/catalogrule')->validateItem($productId);
		return $catalogRule;
	}
	
	public function getFreeGifts($productId)
	{
		$ruleId = $this->getRuleByPruduct($productId)->getId();
		$freeGrift = Mage::getModel('promotionalgift/catalogitem')->load($ruleId, 'rule_id')->getProductIds();
		$freeGrift = explode(',', $freeGrift);
		return $freeGrift;
	}
}