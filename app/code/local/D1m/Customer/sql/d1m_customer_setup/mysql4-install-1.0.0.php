<?php
/** @var $this Mage_Customer_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

//$installer->addAttribute('customer', 'country', 'varchar');
//$installer->addAttribute('customer', 'area_code', 'varchar');
//$installer->addAttribute('customer', 'mobile', 'varchar');

$installer->addAttribute('customer_address', 'area_code', 'varchar');

$installer->endSetup();