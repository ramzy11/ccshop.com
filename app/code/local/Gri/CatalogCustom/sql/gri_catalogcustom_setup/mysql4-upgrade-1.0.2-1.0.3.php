<?php
$installer = $this;
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->startSetup();
/*
 * check whether best_seller and editors_pick exist, update ortherwise create
 */

$best_seller_id = Mage::getModel('eav/entity_attribute')->getIdByCode('catalog_product','best_seller');
if($best_seller_id) {
	$setup->updateAttribute('4', $best_seller_id, 'backend_type','int');
} else {
	$setup->addAttribute('4', 'best_seller', array(
		'group'         => 'general',
		'input'         => 'text',
		'type'          => 'int',
		'label'         => 'Best Seller',
		'backend'       => '',
		'visible'       => 0,
		'required'      => 0,
		'user_defined' => 1,
		'searchable' => 0,
		'filterable' => 0,
		'comparable'    => 0,
		'visible_on_front' => 0,
		'visible_in_advanced_search'  => 0,
		'is_html_allowed_on_front' => 0,
		'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
	));
}

$editors_pick_id = Mage::getModel('eav/entity_attribute')->getIdByCode('catalog_product','editors_pick');
if($editors_pick_id) {
	$setup->updateAttribute('4', $editors_pick_id, 'backend_type','int');
	} else {
		$setup->addAttribute('4', 'editors_pick', array(
			'group'         => 'general',
			'input'         => 'text',
			'type'          => 'int',
			'label'         => 'Editors Pick',
			'backend'       => '',
			'visible'       => 0,
			'required'      => 0,
			'user_defined' => 1,
			'searchable' => 0,
			'filterable' => 0,
			'comparable'    => 0,
			'visible_on_front' => 0,
			'visible_in_advanced_search'  => 0,
			'is_html_allowed_on_front' => 0,
			'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
		));
	}
$installer->endSetup();
