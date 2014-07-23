<?php

$installer = $this;

$installer->startSetup();


/**
* Create table '
*/
$installer->run("
CREATE TABLE IF NOT EXISTS `{$installer->getTable('gri_ubl')}` 
(
`user_id` int(11) unsigned NOT NULL,
`locale` varchar(10) NOT NULL DEFAULT 'en_US',
PRIMARY KEY (`user_id`)
)ENGINE=InnoDB DEFAULT CHARSET='utf8';
");
	
$installer->endSetup();

