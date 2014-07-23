<?php
/* @var $this Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$flashSaleProductTable = $installer->getTable('gri_flashsale/flashsale_product');
$flashSaleProductOrderedTable = $installer->getTable('gri_flashsale/flashsale_product_ordered');

$installer->run("
ALTER TABLE `{$flashSaleProductTable}`
ADD COLUMN `color_qty` DECIMAL(10,4) UNSIGNED AFTER `parent_qty`
");
$installer->run("
ALTER TABLE `{$flashSaleProductOrderedTable}`
ADD COLUMN `color_qty_ordered` DECIMAL(10,4),
ADD COLUMN `color_code` INT(10) UNSIGNED AFTER `parent_id`
");

$installer->endSetup();
