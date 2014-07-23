<?php
$installer = $this;
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->startSetup();

//$attributeModel = Mage::getModel('eav/entity_attribute')->loadByCode($entity_type, $attributeCode);
$color_filter_1 = Mage::getModel('eav/entity_attribute')->loadByCode('catalog_product','color_filter_1');
$color_filter_2 = Mage::getModel('eav/entity_attribute')->loadByCode('catalog_product','color_filter_2');
$price = Mage::getModel('eav/entity_attribute')->loadByCode('catalog_product','price');
$data = array($color_filter_1,$color_filter_2,$price);
foreach($data as $v) {
	$v->setData('is_filterable_in_search',1)->save();
	//$setup->updateAttribute(4, $id, 'is_filterable_in_search',1);
}
$installer->endSetup();

