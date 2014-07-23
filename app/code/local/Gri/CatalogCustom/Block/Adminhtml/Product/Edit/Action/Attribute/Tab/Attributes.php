<?php
class Gri_CatalogCustom_Block_Adminhtml_Product_Edit_Action_Attribute_Tab_Attributes extends Mage_Adminhtml_Block_Catalog_Product_Edit_Action_Attribute_Tab_Attributes
{
	public function getAttributes() {
		$attributes = Mage::getSingleton('eav/config')
		->getEntityType(Mage_Catalog_Model_Product::ENTITY)
		->getAttributeCollection()
		->addIsNotUniqueFilter()
		->addFieldToFilter('attribute_code',array("eq" => "editors_pick"));
		return $attributes->getItems();
	}
}