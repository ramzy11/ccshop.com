<?php
/* @var $this Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

/* @var $country Mage_Directory_Model_Country */
$country = Mage::getModel('directory/country');
$country->loadByCode('CN');
/* @var $region Mage_Directory_Model_Region */
$region = Mage::getModel('directory/region');
$region->loadByCode('310000', $country->getId());
/* @var $city Gri_Directory_Model_City */
$city = Mage::getModel('gri_directory/city');
$tableRateTable = $installer->getTable('shipping/tablerate');
$installer->getConnection()->insertOnDuplicate($tableRateTable, array(
    array(
        'website_id' => 1,
        'dest_country_id' => 'CN',
        'dest_region_id' => $region->getId(),
        'dest_zip' => '221000',
        'condition_name' => 'package_weight',
        'condition_value' => 0,
        'price' => 20,
        'cost' => 0,
    ),
    array(
        'website_id' => 1,
        'dest_country_id' => 'CN',
        'dest_region_id' => $region->getId(),
        'dest_zip' => '222000',
        'condition_name' => 'package_weight',
        'condition_value' => 0,
        'price' => 20,
        'cost' => 0,
    ),
    array(
        'website_id' => 1,
        'dest_country_id' => 'CN',
        'dest_region_id' => $region->getId(),
        'dest_zip' => '223000',
        'condition_name' => 'package_weight',
        'condition_value' => 0,
        'price' => 20,
        'cost' => 0,
    ),
));

$installer->endSetup();
