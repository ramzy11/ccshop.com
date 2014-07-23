<?php
/* @var $this Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$flashSaleProductTable = $installer->getTable('gri_flashsale/flashsale_product');

$installer->run("
ALTER TABLE `{$flashSaleProductTable}`
ADD COLUMN `color_code` INT(10) UNSIGNED,
ADD COLUMN `size_clothing` INT(10) UNSIGNED,
ADD COLUMN `size_shoes` INT(10) UNSIGNED,
ADD COLUMN `color_label` VARCHAR(255),
ADD COLUMN `size_clothing_label` VARCHAR(255),
ADD COLUMN `size_shoes_label` VARCHAR(255),
ADD COLUMN `parent_id` INT(10) UNSIGNED,
ADD CONSTRAINT `FK_GRI_FLASHSALE_PRODUCT_PRODUCT_ID` FOREIGN KEY (product_id) REFERENCES {$installer->getTable('catalog/product')}( `entity_id` ) ON DELETE CASCADE ON UPDATE CASCADE;
");

$installer->endSetup();
