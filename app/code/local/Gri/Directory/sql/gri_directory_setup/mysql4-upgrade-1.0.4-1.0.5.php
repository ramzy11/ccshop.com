<?php
/* @var $this Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$CountryRegionTable = $installer->getTable('directory/country_region');
$regionCityTable = $installer->getTable('gri_directory/region_city');

$sql = "
 delete from `{$CountryRegionTable}` where `country_id`='SG';
 delete from `{$regionCityTable}` where `country_id`='SG';
 insert into `{$CountryRegionTable}`(`country_id`,`code`,`default_name`) values ('SG','sg01','Singapore');
";

$installer->run($sql);

$installer->endSetup();
