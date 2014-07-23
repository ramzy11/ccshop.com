<?php
/* @var $this Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$CountryRegionTable = $installer->getTable('directory/country_region');
$regionCityTable = $installer->getTable('gri_directory/region_city');
$cityNameTable = $installer->getTable('gri_directory/city_name');
$sql = "CREATE TABLE `{$regionCityTable}` (
 `city_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'City Id',
 `region_id` int(10) unsigned NOT NULL COMMENT 'Region Id',
 `country_id` varchar(4) NOT NULL COMMENT 'Country Id in ISO-2',
 `code` varchar(32) DEFAULT NULL COMMENT 'City code',
 `default_name` varchar(255) DEFAULT NULL COMMENT 'City Name',
 PRIMARY KEY (`city_id`),
 KEY `region_id` (`region_id`),
 KEY `country_id` (`country_id`),
 CONSTRAINT `FK_DIRECTORY_REGION_CITY_DIRECTORY_COUNTRY_REGION_REGION_ID` FOREIGN KEY (`region_id`) REFERENCES `{$CountryRegionTable}` (`region_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `{$cityNameTable}` (
 `locale` varchar(8) NOT NULL DEFAULT '' COMMENT 'Locale',
 `city_id` int(10) unsigned NOT NULL COMMENT 'City Id',
 `name` varchar(255) DEFAULT NULL COMMENT 'City Name',
 PRIMARY KEY (`locale`, `city_id`),
 KEY `city_id` (`city_id`),
 CONSTRAINT `FK_DIRECTORY_REGION_CITY_NAME_DIRECTORY_REGION_CITY_CITY_ID` FOREIGN KEY (`city_id`) REFERENCES `{$regionCityTable}` (`city_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
$installer->run($sql);

$installer->endSetup();
