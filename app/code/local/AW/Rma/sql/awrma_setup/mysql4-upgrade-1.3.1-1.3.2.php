<?php
/* @var $this Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();
$rmaTable = $installer->getTable('awrma/entity');
$statusTable = $installer->getTable('awrma/entity_status');
$typesTable = $installer->getTable('awrma/entity_types');
$installer->run("
ALTER TABLE `{$rmaTable}` ADD COLUMN `updated_at` datetime AFTER `created_at`;
UPDATE `{$statusTable}` SET `name` = 'Resolved (Canceled)' WHERE `id` = '4';
UPDATE `{$statusTable}` SET `name` = 'Resolved (Returned)' WHERE `id` = '5';
UPDATE `{$statusTable}` SET `name` = 'Resolved (Exchanged)' WHERE `id` = '6';
INSERT INTO `{$statusTable}` (`id`, `name`, `store`, `sort`, `resolve`) VALUES
(7, 'BDT Notified', 0, 3, 0),
(8, 'Resolved (Canceled and BDT Notified)', 0, 8, 1);
UPDATE `{$typesTable}` SET `name` = 'Exchange' WHERE `id` = '1';
UPDATE `{$typesTable}` SET `name` = 'Return' WHERE `id` = '2';
");
$installer->endSetup();
