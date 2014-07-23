<?php
/* @var $this Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();
if (!$installer->tableExists($installer->getTable('country_group'))) {
	$installer->run("
		-- DROP TABLE IF EXISTS {$this->getTable('country_group')};
		CREATE TABLE {$this->getTable('country_group')} (
		`country_group_id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
		`name` VARCHAR( 255 ) NOT NULL ,
		`value` VARCHAR( 255 ) NULL ,
		UNIQUE (`name`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='country group';
		");
}
