<?php
/* @var $this Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$flashSaleTable = $installer->getTable('gri_flashsale/flashsale');

$installer->run("
    ALTER TABLE `{$flashSaleTable}` ADD COLUMN `mobile_image` varchar(255);
");

$installer->endSetup();
