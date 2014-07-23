<?php
/* @var $this Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$flashSaleTable = $installer->getTable('gri_flashsale/flashsale');
$flashSaleProductTable = $installer->getTable('gri_flashsale/flashsale_product');
$sql = "CREATE TABLE IF NOT EXISTS `{$flashSaleTable}` (
  `flash_sale_id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(255),
  `image` varchar(255),
  `small_image` varchar(255),
  `start` timestamp NOT NULL,
  `end` timestamp NOT NULL,
  `created_at` timestamp NOT NULL,
  `updated_at` timestamp NOT NULL,
  `is_active` tinyint(1) unsigned NOT NULL default '0',
  `definition` text,
  PRIMARY KEY  (`flash_sale_id`),
  KEY `start` (`start`),
  KEY `end` (`end`),
  KEY `created_at` (`created_at`),
  KEY `updated_at` (`updated_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
$installer->run($sql);

$sql = "CREATE TABLE IF NOT EXISTS `{$flashSaleProductTable}` (
  `flash_sale_id` int(10) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  `price` decimal(12,4) NOT NULL,
  PRIMARY KEY  (`flash_sale_id`, `product_id`),
  CONSTRAINT `FK_GRI_FLASHSALE_PRODUCT_GRI_FLASHSALE_ID` FOREIGN KEY (`flash_sale_id`) REFERENCES `{$flashSaleTable}` (`flash_sale_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
$installer->run($sql);

$installer->endSetup();
