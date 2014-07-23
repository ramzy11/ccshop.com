<?php
/* @var $this Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$tables = array(
    'order' => $installer->getTable('sales/order'),
    'order_grid' => $installer->getTable('sales/order_grid'),
    'creditmemo' => $installer->getTable('sales/creditmemo'),
    'creditmemo_grid' => $installer->getTable('sales/creditmemo_grid'),
    'rma' => $installer->getTable('awrma/entity'),
);
foreach ($tables as $table) {
    $installer->run("ALTER TABLE `{$table}` ADD COLUMN `api_retry_count` INT(10) UNSIGNED NOT NULL DEFAULT '0';");
}

$installer->endSetup();
