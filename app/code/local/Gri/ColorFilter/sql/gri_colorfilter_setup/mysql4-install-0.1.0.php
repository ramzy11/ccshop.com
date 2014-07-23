<?php
/* @var $this Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$colorFilterTable = $installer->getTable('gri_colorfilter');

$sql = "CREATE TABLE IF NOT EXISTS `{$colorFilterTable}` (
  `color_id` int(10) unsigned NOT NULL auto_increment,
  `label` varchar(255),
  `image` varchar(255),
  `is_active` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`color_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
$installer->run($sql);

$installer->endSetup();
