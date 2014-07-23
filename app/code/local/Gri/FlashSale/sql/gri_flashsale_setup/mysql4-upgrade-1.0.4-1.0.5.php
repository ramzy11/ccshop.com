<?php
/* @var $this Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$flashSaleProductTable = $installer->getTable('gri_flashsale/flashsale_product');

$installer->run("
ALTER TABLE `{$flashSaleProductTable}`
ADD COLUMN `color_filter_1` INT(10) UNSIGNED,
ADD COLUMN `color_filter_2` INT(10) UNSIGNED
");

$installer->endSetup();
