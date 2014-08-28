<?php

$install = $this;
$install->startSetup();

$install->run("
DROP TABLE IF EXISTS $install->getTable('gri_vip/vip_offline_pk');
CREATE TABLE IF NOT EXISTS $install->getTable('gri_vip/vip_offline_pk') (
	`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`customer_id` INT(11) UNSIGNED NOT NULL,
	`offline_vip_id` INT(11) UNSIGNED NOT NULL,
	PRIMARY_KEY (`id`),
	KEY `fk_vipopk_customer` (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

$install->endSetup();
