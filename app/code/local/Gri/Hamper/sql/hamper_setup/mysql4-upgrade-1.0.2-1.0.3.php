<?php

/* @var $this Mage_Catalog_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'hamper_discount', array(
    'type' => 'text',
    'backend' => '',
    'frontend' => '',
    'label' => 'Step Discounts',
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
    'used_in_product_listing' => TRUE,
    'unique' => FALSE,
    'apply_to' => 'hamper',
    'is_configurable' => FALSE
));

$installer->endSetup();
