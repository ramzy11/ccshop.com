<?php
/* @var $this Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$sql = "SHOW COLUMNS FROM `{$this->getTable('gri_api_product')}` WHERE `Field`='is_sent'";
if(!$row = $installer->getConnection()->fetchRow($sql, PDO::FETCH_ASSOC)){
    $sql = "ALTER TABLE `{$this->getTable('gri_api_product')}` add `is_sent` tinyint NOT NULL default 0";
    $installer->run($sql);
}

$installer->endSetup();
