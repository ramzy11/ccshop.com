<?php
/* @var $this Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
$installer = $this;
$installer->startSetup();

// These attributes should not be used for creating configurable product
$attributes = array(
    'brand',
    'country_group',
    'heel_height',
    'size_filter_1',
);
foreach ($attributes as $code) {
    $installer->updateAttribute('catalog_product', $code, array('is_configurable' => 0));
}

$installer->endSetup();
