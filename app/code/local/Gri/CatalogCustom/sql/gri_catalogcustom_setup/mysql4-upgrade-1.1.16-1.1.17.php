<?php
/* @var $this Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
$installer = $this;
$installer->startSetup();

$installer->run("
    ALTER TABLE `{$installer->getTable('catalog_product_entity')}` DROP INDEX `IDX_CATALOG_PRODUCT_ENTITY_SKU`;
    ALTER TABLE `{$installer->getTable('catalog_product_entity')}` ADD UNIQUE INDEX `IDX_CATALOG_PRODUCT_ENTITY_SKU`(`sku`);
");

$installer->endSetup();
