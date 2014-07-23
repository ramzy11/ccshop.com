<?php
/* @var $this Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$griReportOrderTable = $installer->getTable('gri_reports/report_order');
$installer->run("ALTER TABLE `{$griReportOrderTable}` DROP COLUMN `payment_account`;");

$installer->endSetup();
