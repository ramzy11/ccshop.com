<?php
/* @var $this Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$flashSaleProductTable = $installer->getTable('gri_flashsale/flashsale_product');

$installer->run("
ALTER TABLE `{$flashSaleProductTable}`
ADD COLUMN `minimal_price` DECIMAL(12, 4) UNSIGNED
");

$installer->endSetup();
