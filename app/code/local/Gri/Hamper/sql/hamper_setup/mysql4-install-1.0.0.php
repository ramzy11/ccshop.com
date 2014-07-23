<?php

/* @var $this Mage_Catalog_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$fieldList = array(
    'price', 'special_price', 'special_from_date', 'special_to_date',
    'minimal_price', 'cost', 'tier_price', 'weight', 'tax_class_id', 'price_type',
    'sku_type', 'weight_type', 'price_view', 'shipment_type',
);
foreach ($fieldList as $field) {
    $applyTo = explode(',', $installer->getAttribute('catalog_product', $field, 'apply_to'));
    if ($applyTo && !in_array('hamper', $applyTo)) {
        $applyTo[] = 'hamper';
        $installer->updateAttribute('catalog_product', $field, 'apply_to', implode(',', $applyTo));
    }
}

$bundleOptionValueTable = $installer->getTable('bundle/option_value');
$installer->run("ALTER TABLE `{$bundleOptionValueTable}` ADD `image` VARCHAR(255) NULL DEFAULT NULL;");

$installer->endSetup();
