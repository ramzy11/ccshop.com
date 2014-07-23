<?php
/* @var $this Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$griReportOrderItemTable = $installer->getTable('gri_reports/report_order_item');
$installer->run("ALTER TABLE `{$griReportOrderItemTable}` ADD COLUMN `product_created_at` TIMESTAMP NOT NULL;");

$installer->endSetup();
