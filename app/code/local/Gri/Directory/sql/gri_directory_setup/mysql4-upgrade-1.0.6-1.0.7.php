<?php
/* @var $this Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$countryRegionTable = $installer->getTable('directory/country_region');
$regionCityTable = $installer->getTable('gri_directory/region_city');

$installer->run("ALTER TABLE `{$countryRegionTable}` ADD COLUMN `is_active` SMALLINT(1) UNSIGNED NOT NULL DEFAULT '1';");
$installer->run("ALTER TABLE `{$regionCityTable}` ADD COLUMN `is_active` SMALLINT(1) UNSIGNED NOT NULL DEFAULT '1';");
$installer->run("UPDATE `{$countryRegionTable}` SET `is_active` = 0 WHERE `code` = 'PICKUP';");

$installer->getConnection()->commit();

$installer->endSetup();
