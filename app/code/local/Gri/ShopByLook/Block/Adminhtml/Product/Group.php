<?php
class Gri_ShopByLook_Block_Adminhtml_Product_Group extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Group
{
	public function _prepareCollection() {
		$allowProductTypes = array();
		$allowProductTypeNodes = Mage::getConfig()
		->getNode('global/catalog/product/type/grouped/allow_product_types')->children();
		foreach ($allowProductTypeNodes as $type) {
			$allowProductTypes[] = $type->getName();
		}
		$visibility = array(
			Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
			Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG
		);
		$collection = Mage::getModel('catalog/product_link')->useGroupedLinks()
		->getProductCollection()
		->setProduct($this->_getProduct())
		->addAttributeToSelect('*')
		->addAttributeToFilter('type_id', $allowProductTypes)
		->addAttributeToFilter('visibility',$visibility);
		if ($this->getIsReadonly() === true) {
			$collection->addFieldToFilter('entity_id', array('in' => $this->_getSelectedProducts()));
		}
		$this->setCollection($collection);
		return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
	}
}