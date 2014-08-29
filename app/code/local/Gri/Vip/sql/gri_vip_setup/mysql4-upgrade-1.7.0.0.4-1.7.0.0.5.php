<?php

$install = $this;
$install->startSetup();

$install->run("
DROP TABLE IF EXISTS $install->getTable('gri_vip/offline_pk');
CREATE TABLE IF NOT EXISTS $install->getTable('gri_vip/offline_pk') (
	`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`customer_id` INT(11) UNSIGNED NOT NULL,
	`offline_vip_id` INT(11) UNSIGNED NOT NULL,
	`vip_card_no` VARCHAR(16) DEFAULT NULL,
	`vip_grade` VARCHAR(12) DEFAULT 'GREY',
	`vip_point` double(10,1) DEFAULT '0.0',
	`vip_redeemed_point` double(10,1) DEFAULT '0.0',
	`vip_earned_point` double(10,1) DEFAULT '0.0',
	`expiry_date` date DEFAULT NULL,
	`last_update` date DEFAULT NULL,
	PRIMARY KEY (`id`),
	KEY `fk_vipopk_customer` (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

$install->endSetup();
