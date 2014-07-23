<?php
/* @var $this Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$sql = "CREATE TABLE `{$this->getTable('gri_api_product')}`(
           `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
           `transaction_id` CHAR(32),
           `json` MEDIUMTEXT,
           `status` TINYINT NOT NULL DEFAULT 0,
           `created_at` TIMESTAMP,
           `error_info` VARCHAR(255) NOT NULL DEFAULT '',
           PRIMARY KEY  (`id`) ,
           KEY `key_created_at`(`created_at`)
       )ENGINE=InnoDB DEFAULT CHARSET=utf8;";

$installer->run($sql);

$installer->endSetup();
