<?php

class Magestore_Promotionalgift_Model_Layer extends Mage_Catalog_Model_Layer
{
    public function getProductCollection()
    {
		if (isset($this->_productCollections[$this->getCurrentCategory()->getId()])) {
            $collection = $this->_productCollections[$this->getCurrentCategory()->getId()];
        } else {
            $collection = Mage::getModel('catalog/product')
								->getCollection()
								->addAttributeToSelect('*')     
								->addAttributeToFilter('entity_id', array('in'=>$this->getProductIds()));			
            $this->prepareProductCollection($collection);
            $this->_productCollections[$this->getCurrentCategory()->getId()] = $collection;
        }
		
		// Zend_Debug::dump($collection->getSelect()->__toString());die();
		return $collection;
		
    }
	
	public function getProductIds()
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
	
	public function _getProductCollection()
    {	
			$collection  = Mage::getResourceModel('catalog/product_collection')
								->addAttributeToSelect('*')
								->addFieldToFilter('entity_id', array('in'=>$this->getProductIds()));								
		return $collection;	
    }
	
}
