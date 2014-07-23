<?php
/* @var $this Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();
$offlineVipTable = $installer->getTable('gri_vip/relation_offline');

$installer->run("
ALTER TABLE `{$offlineVipTable}`
ADD COLUMN `state` tinyint(1) UNSIGNED NOT NULL DEFAULT 1
");

$installer->endSetup();
