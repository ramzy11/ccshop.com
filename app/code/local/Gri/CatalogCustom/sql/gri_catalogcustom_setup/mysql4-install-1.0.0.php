<?php
$installer = $this;
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
$installer->startSetup();
if (!$installer->tableExists($installer->getTable('size_mapping'))) {
	$installer->run("
		-- DROP TABLE IF EXISTS {$this->getTable('size_mapping')};
		CREATE TABLE {$this->getTable('size_mapping')} (
		`mapping_id` int(10) unsigned NOT NULL auto_increment,
		`admin_size` varchar(255) NOT NULL default '',
		`universal_size` varchar(255) NOT NULL default '',
		`mapping_count` int(10) unsigned NOT NULL default '0',
		PRIMARY KEY  (`mapping_id`),
		KEY `FK_catalog_category_ENTITY_ENTITY_TYPE` (`admin_size`),
		KEY `FK_catalog_category_ENTITY_STORE` (`universal_size`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Size mapping';
		");
}
?>
