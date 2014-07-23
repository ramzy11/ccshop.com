<?php
/* @var $this Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$orderTable = $installer->getTable('sales/order');
$orderItemTable = $installer->getTable('sales/order_item');

$orderReportTable = $installer->getTable('gri_reports/report_order');
$orderItemReportTable = $installer->getTable('gri_reports/report_order_item');

$installer->run("
CREATE TABLE IF NOT EXISTS `{$orderReportTable}` (
  `order_id` int(10) unsigned NOT NULL,
  `order_created_day` date NOT NULL,
  `order_created_month` date NOT NULL,
  `order_created_year` date NOT NULL,
  `order_shipping_day` date NOT NULL,
  `order_shipping_month` date NOT NULL,
  `order_shipping_year` date NOT NULL,
  `payment_account` varchar(255),
  PRIMARY KEY (`order_id`),
  KEY `order_created_day` (`order_created_day`),
  KEY `order_shipping_day` (`order_shipping_day`),
  CONSTRAINT `FK_GRI_REPORT_ORDER_ORDER_ID` FOREIGN KEY (`order_id`) REFERENCES `{$orderTable}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$orderItemReportTable}` (
  `order_item_id` int(10) unsigned NOT NULL,
  `order_created_day` date NOT NULL,
  `order_created_month` date NOT NULL,
  `order_created_year` date NOT NULL,
  `order_shipping_day` date NOT NULL,
  `order_shipping_month` date NOT NULL,
  `order_shipping_year` date NOT NULL,
  `style_no` varchar(255) NOT NULL,
  `style_name` varchar(255) NOT NULL,
  `color` varchar(255) NOT NULL,
  `size` varchar(255) NOT NULL,
  `price` varchar(255) NOT NULL,
  `brand` varchar(255) NOT NULL,
  `base_category` varchar(255) NOT NULL,
  `sub_category` varchar(255) NOT NULL,
  `bottom_category` varchar(255) NOT NULL,
  `base_category_id` int(10) unsigned NOT NULL,
  `sub_category_id` int(10) unsigned NOT NULL,
  `bottom_category_id` int(10) unsigned,
  `payment_account` varchar(255),
  PRIMARY KEY (`order_item_id`),
  KEY `order_created_day` (`order_created_day`),
  KEY `order_shipping_day` (`order_shipping_day`),
  KEY `style_no` (`style_no`),
  CONSTRAINT `FK_GRI_REPORT_ORDER_ITEM_ORDER_ITEM_ID` FOREIGN KEY (`order_item_id`) REFERENCES `{$orderItemTable}` (`item_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();
