<?php
$installer = $this;
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->startSetup();
/**
 * Adding Different Attributes
 */
$setup->addAttribute('4', 'search_priority', array(
	'group'         => 'general',
	'input'         => 'text',
	'type'          => 'int',
	'label'         => 'Search Priority',
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
$installer->endSetup();
