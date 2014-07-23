<?php
/* @var $this Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$productTable = $installer->getTable('catalog/product');
$flashSaleTable = $installer->getTable('gri_flashsale/flashsale');
$flashSaleProductTable = $installer->getTable('gri_flashsale/flashsale_product');
$flashSaleProductOrderedTable = $installer->getTable('gri_flashsale/flashsale_product_ordered');

$installer->run("
ALTER TABLE `{$flashSaleProductTable}`
ADD COLUMN `qty` decimal(10, 4) UNSIGNED,
ADD COLUMN `parent_qty` decimal(10, 4) UNSIGNED,
ADD CONSTRAINT `FK_GRI_FLASHSALE_PRODUCT_PARENT_PRODUCT_ID` FOREIGN KEY (`parent_id`) REFERENCES `{$productTable}` ( `entity_id` ) ON DELETE CASCADE ON UPDATE CASCADE;
");

$installer->run("
CREATE TABLE IF NOT EXISTS `{$flashSaleProductOrderedTable}` (
  `flash_sale_id` int(10) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  `parent_id` int(10) unsigned NOT NULL,
  `qty_ordered` decimal(12, 4) NOT NULL,
  `parent_qty_ordered` decimal(12, 4) NOT NULL,
  PRIMARY KEY (`flash_sale_id`, `product_id`),
  CONSTRAINT `FK_GRI_FLASHSALE_PRODUCT_ORDERED_GRI_FLASHSALE_ID` FOREIGN KEY (`flash_sale_id`) REFERENCES `{$flashSaleTable}` (`flash_sale_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_GRI_FLASHSALE_PRODUCT_ORDERED_PRODUCT_ID` FOREIGN KEY (`product_id`) REFERENCES `{$productTable}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_GRI_FLASHSALE_PRODUCT_ORDERED_PARENT_PRODUCT_ID` FOREIGN KEY (`parent_id`) REFERENCES `{$productTable}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();
