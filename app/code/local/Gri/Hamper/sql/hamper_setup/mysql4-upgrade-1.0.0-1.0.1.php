<?php

/* @var $this Mage_Catalog_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'extra_gifts', array(
    'type' => 'varchar',
    'backend' => '',
    'frontend' => '',
    'label' => 'Extra Gift SKUs',
    'input' => '',
    'class' => '',
    'source' => '',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => FALSE,
    'required' => FALSE,
    'user_defined' => FALSE,
    'default' => '',
    'searchable' => FALSE,
    'filterable' => FALSE,
    'comparable' => FALSE,
    'visible_on_front' => TRUE,
    'used_in_product_listing' => FALSE,
    'unique' => FALSE,
    'apply_to' => 'hamper',
    'is_configurable' => FALSE
));

$installer->endSetup();
