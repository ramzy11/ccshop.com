<?php
/* @var $this Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$flashSaleTable = $installer->getTable('gri_flashsale/flashsale');

$installer->run("
    ALTER TABLE `{$flashSaleTable}` ADD COLUMN `mobile_small_image` varchar(255);
    ALTER TABLE `{$flashSaleTable}` ADD COLUMN `image_cht` varchar(255);
    ALTER TABLE `{$flashSaleTable}` ADD COLUMN `small_image_cht` varchar(255);
    ALTER TABLE `{$flashSaleTable}` ADD COLUMN `mobile_image_cht` varchar(255);
    ALTER TABLE `{$flashSaleTable}` ADD COLUMN `mobile_small_image_cht` varchar(255);
");

$installer->endSetup();
