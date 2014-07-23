<?php
/* @var $this Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();
$rmaTable = $installer->getTable('awrma/entity');
$installer->run("
ALTER TABLE `{$rmaTable}` ADD COLUMN `exchange_items` text AFTER `order_items`;
");
$installer->endSetup();
