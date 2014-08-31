<?php
/** @var $this Mage_Customer_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$installer->updateAttribute('customer_address', 'area_code',array(
    'visible' => true,
    'label'=>'area_code',
    'type' => 'varchar',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'user_defined' => true,
    'option' => array ()
));
$oAttribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'title');
$oAttribute->setData('used_in_forms', array('checkout_register','customer_account_create','customer_account_edit','adminhtml_customer'));
$oAttribute->save();

$oAttribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'customer_country_id');
$oAttribute->setData('used_in_forms', array('checkout_register','customer_account_create','customer_account_edit','adminhtml_customer'));
$oAttribute->save();

$oAttribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'customer_area_code');
$oAttribute->setData('used_in_forms', array('checkout_register','customer_account_create','customer_account_edit','adminhtml_customer'));
$oAttribute->save();

$oAttribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'mobile');
$oAttribute->setData('used_in_forms', array('checkout_register','customer_account_create','customer_account_edit','adminhtml_customer'));
$oAttribute->save();

$oAttribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'mailing_address');
$oAttribute->setData('used_in_forms', array('checkout_register','customer_account_create','customer_account_edit','adminhtml_customer'));
$oAttribute->save();

$oAttribute = Mage::getSingleton('eav/config')->getAttribute('customer_address', 'area_code');
$oAttribute->setData('used_in_forms', array('customer_address_edit','adminhtml_customer_address','customer_register_address'));
$oAttribute->save();
$installer->getConnection()
    ->addColumn(
    $installer->getTable('sales/quote'),
    'customer_country_id',
     'varchar(200) NULL'
);
$installer->getConnection()->addColumn(
    $installer->getTable('sales/quote'),
    'customer_area_code',
    'varchar(20) NULL'
);
$installer->getConnection()->addColumn(
    $installer->getTable('sales/quote'),
    'customer_mobile',
    'varchar(30) NULL'
);
$installer->getConnection()->addColumn(
    $installer->getTable('sales/quote'),
    'customer_title',
    'varchar(30) NULL'
);
$installer->getConnection()->addColumn(
    $installer->getTable('sales/quote'),
    'customer_mailing_address',
    'tinytext NULL'
);
$installer->endSetup();