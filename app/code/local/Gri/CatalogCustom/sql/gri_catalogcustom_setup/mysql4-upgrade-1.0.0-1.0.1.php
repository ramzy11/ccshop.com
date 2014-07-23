<?php
$installer = $this;
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
$installer->startSetup();
if (!$installer->tableExists($installer->getTable('size_mapping'))) {
	$installer->run("
		-- DROP TABLE IF EXISTS {$this->getTable('size_mapping')};
		CREATE TABLE {$this->getTable('size_mapping')} (
		`mapping_id` INT( 10 ) NOT NULL AUTO_INCREMENT ,
		`admin_size` VARCHAR( 255 ) NOT NULL ,
		`universal_size` VARCHAR( 255 ) DEFAULT NULL,
		`mapping_count` INT( 10 ) DEFAULT NULL,
		PRIMARY KEY  (`mapping_id`),
		KEY `admin_size` (`admin_size`),
		KEY `universal_size` (`universal_size`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='size mapping';
		");
}