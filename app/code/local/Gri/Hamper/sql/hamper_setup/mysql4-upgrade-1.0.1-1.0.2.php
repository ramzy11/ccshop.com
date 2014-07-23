<?php

/* @var $this Mage_Catalog_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'hamper_pick_limit', array(
    'type' => 'int',
    'backend' => '',
    'frontend' => '',
    'label' => 'Pick Limitation',
    'input' => '',
    'class' => '',
    'source' => '',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => FALSE,
    'required' => FALSE,
    'user_defined' => FALSE,
    'default' => '0',
    'searchable' => FALSE,
    'filterable' => FALSE,
    'comparable' => FALSE,
    'visible_on_front' => TRUE,
    'used_in_product_listing' => TRUE,
    'unique' => FALSE,
    'apply_to' => 'hamper',
    'is_configurable' => FALSE
));

/* @var $contentHelper Gri_Cms_Helper_Content */
$contentHelper = Mage::helper('gri_cms/content');

Mage::app()->reinitStores();
foreach (Mage::app()->getStores() as $storeId => $store) {
    if ($storeId <= 1) continue;
    $contentHelper->updateStoreBlocks($store, array(
        'hamper-whats-more',
        'hamper-terms-condition',
    ));
}

$installer->endSetup();
