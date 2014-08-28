<?php
/* @var $this Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();
$onlineVipTable = $installer->getTable('gri_vip/relation_online');
$offlineVipTable = $installer->getTable('gri_vip/relation_offline');
$customerTable = $installer->getTable('customer/entity');

$installer->run("ALTER TABLE {$offlineVipTable} DROP KEY `uk_card_no`;");
$installer->run("
ALTER TABLE `{$onlineVipTable}`
ADD UNIQUE KEY `customer_id` (`customer_id`),
ADD COLUMN `state` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
ADD CONSTRAINT `FK_GRI_ONLINE_VIP_CUSTOMER_ENTITY_CUSTOMER_ID` FOREIGN KEY (`customer_id`) REFERENCES `{$customerTable}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE;
");

$installer->endSetup();
