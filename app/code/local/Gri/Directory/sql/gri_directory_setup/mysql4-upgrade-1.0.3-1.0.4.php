<?php
/* @var $this Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$CountryRegionTable = $installer->getTable('directory/country_region');
$sql = "
UPDATE `{$CountryRegionTable}` SET `default_name` = 'Kowloon' WHERE `country_id` = 'HK' AND `code` = 'KLN';
UPDATE `{$CountryRegionTable}` SET `default_name` = 'New Territories' WHERE `country_id` = 'HK' AND `code` = 'NT';
";
$installer->run($sql);

$installer->endSetup();
