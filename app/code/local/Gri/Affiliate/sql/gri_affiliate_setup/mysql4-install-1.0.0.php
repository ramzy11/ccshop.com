<?php
/* @var $this Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$affiliateOrderTable = $installer->getTable('gri_affiliate/order');
$orderTable = $installer->getTable('sales/order');
$installer->run("CREATE TABLE IF NOT EXISTS `{$affiliateOrderTable}` (
 `entity_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Affiliate Order Id',
 `order_id` int(10) unsigned NOT NULL COMMENT 'Order Id',
 `affiliate` varchar(255) NOT NULL DEFAULT '' COMMENT 'Affiliate code',
 `landing_page` varchar(255) NOT NULL DEFAULT '' COMMENT 'Affiliate landing page',
 `hash` varchar(32) NOT NULL DEFAULT '' COMMENT 'Affiliate hash',
 `is_sent` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Real time order data sent or not',
 `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
 `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
 `order_success_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
 PRIMARY KEY (`entity_id`),
 UNIQUE KEY `order_id` (`order_id`),
 KEY `affiliate` (`affiliate`),
 KEY `hash` (`affiliate`),
 CONSTRAINT `FK_AFFILIATE_ORDER_ORDER_ID_SALES_ORDER_ORDER_ID` FOREIGN KEY (`order_id`) REFERENCES `{$orderTable}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$installer->endSetup();
