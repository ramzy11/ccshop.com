<?php
class Gri_CatalogCustom_Block_Adminhtml_Product_Edit_Tab_Attributes extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Attributes
{
	public function _prepareForm(){
		return parent::_prepareForm();
		/*
		$form = $this->getForm();
		$attributes = $this->getGroupAttributes();
		$attributeSetName = Mage::getModel("eav/entity_attribute_set")->load(Mage::registry('product')->getAttributeSetId())->getAttributeSetName();
		foreach($attributes as $attribute) {
			if($attribute->getAttributeCode()=='size') {
				$values = $form->getElement('size')->getValues();
				$newValues = array();
				foreach($values as $options) {
					if(in_array($attributeSetName,explode('-', $options['label'])))
						$newValues[] = array('value' => $options['value'],'label' => $options['label']);
				}
				$form->getElement('size')->setValues($newValues);
			}
		}
		$this->setForm($form);
		*/
	}
}