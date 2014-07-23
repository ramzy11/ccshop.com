<?php
class Gri_CatalogCustom_Block_Product_List_Bestseller extends Mage_Catalog_Block_Product_Abstract
{
		public function getBestSellerCollection($count)
		{
			$collection = array();
			$product = Mage::registry('product');
			$Brand =  Mage::getModel('catalog/category')->loadByAttribute('name',$product->getAttributeText('brand'));
			if($Brand != null) $collection = $Brand->getBestSellerCollection($count);
			return $collection;
		}
}