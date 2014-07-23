<?php

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$sql = "ALTER TABLE `{$installer->getTable('catalog/product_attribute_media_gallery_value')}` ADD COLUMN `swatch` int(10) UNSIGNED NOT NULL DEFAULT '0';";

$installer->run($sql);

$installer->endSetup();
