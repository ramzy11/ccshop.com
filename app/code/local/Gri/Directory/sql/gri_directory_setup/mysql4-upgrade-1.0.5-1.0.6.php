<?php
/* @var $this Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$countryRegionTable = $installer->getTable('directory/country_region'); //  Chinese
$countryRegionNameTable = $installer->getTable('directory/country_region_name'); //  English

$regionCityTable = $installer->getTable('gri_directory/region_city'); //  Chinese
$cityNameTable = $installer->getTable('gri_directory/city_name'); // English


$chineseRegions = array(
    // Hong Kong
    'HK' => array(
        array('default_name' => 'Central/Central Store', 'locale_name' => 'Central/Central 店舖', 'type' => 'region', 'code' => 'PICKUP', 'region' => 'PICKUP'),
        array('default_name' => 'Central Building, Central', 'locale_name' => '中環中建大廈', 'type' => 'city', 'code' => '100', 'region' => 'PICKUP'),
    ),
);

$regionCodesToId = array();
$installer->getConnection()->beginTransaction();
foreach ($chineseRegions as $countryCode => $data) {
    $locale = 'zh_HK';
    foreach ($data as $_data) {
        if ($_data['type'] == 'region') {
            // Insert  English
            $sql = "INSERT INTO `{$countryRegionTable}` (`country_id`, `code`, `default_name`) VALUES ('{$countryCode}', '{$_data['code']}', '{$_data['default_name']}');";
            $installer->run($sql);
            $regionCodesToId[$_data['region']] = $regionId = $installer->getConnection()->lastInsertId();

            // Handle  Chinese
            if (isset($_data['locale_name'])) {
                $sql = "INSERT INTO `{$countryRegionNameTable}` (`locale`, `region_id`, `name`) VALUES ('{$locale}', '{$regionId}', '{$_data['locale_name']}');";
                $installer->run($sql);
            }
        } else {
            // Insert  English
            $sql = "INSERT INTO `{$regionCityTable}` (`region_id`, `country_id`, `code`, `default_name`) VALUES ('{$regionCodesToId[$_data['region']]}', '{$countryCode}', '{$_data['code']}', '{$_data['default_name']}');";
            $installer->run($sql);

            // Handle  Chinese
            if (isset($_data['locale_name'])) {
                $cityId = $installer->getConnection()->lastInsertId();
                $sql = "INSERT INTO `{$cityNameTable}` (`locale`, `city_id`, `name`) VALUES ('{$locale}','{$cityId}','{$_data['locale_name']}');";
                $installer->run($sql);
            }
        }
    }
}
$installer->getConnection()->commit();

$installer->endSetup();
