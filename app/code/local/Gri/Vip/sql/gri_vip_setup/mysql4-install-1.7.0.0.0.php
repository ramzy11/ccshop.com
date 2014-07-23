<?php

/**
 * Magento Gri Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Gri Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/gri-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Gri
 * @package     Gri_Vip
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/gri-edition
 */
/* @var $installer Mage_Customer_Model_Entity_Setup */
$installer = $this;
$installer->startSetup();

$installer->run("
       
DROP TABLE IF EXISTS `{$installer->getTable('gri_vip_relation_online')}` ;
CREATE  TABLE IF NOT EXISTS `{$installer->getTable('gri_vip_relation_online')}` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `card_no` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'no of on-line is different from  no of off-line' ,
  `customer_id` INT(10) UNSIGNED NOT NULL DEFAULT 0 ,
  `create_time` TIMESTAMP NOT NULL DEFAULT '2012-01-01 00:00:00' ,
  `annual_time` TIMESTAMP NOT NULL DEFAULT '2012-01-01 00:00:00' COMMENT 'next annual time' ,
  `update_time` TIMESTAMP NOT NULL DEFAULT '2012-01-01 00:00:00' ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `card_no` (`card_no` ASC) )
ENGINE = InnoDB;


DROP TABLE IF EXISTS  `{$installer->getTable('gri_vip_relation_offline')}` ;
CREATE  TABLE IF NOT EXISTS `{$installer->getTable('gri_vip_relation_offline')}` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `card_no` VARCHAR(255) NOT NULL DEFAULT '' ,
  `mobilephone` BIGINT UNSIGNED NOT NULL DEFAULT 0 ,
  `customer_id` INT(10) UNSIGNED NOT NULL DEFAULT 0 ,
  `create_time` TIMESTAMP NOT NULL DEFAULT '2012-01-01 00:00:00' ,
  `update_time` TIMESTAMP NOT NULL DEFAULT '2012-01-01 00:00:00' ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `card_no_UNIQUE` (`card_no` ASC) )
ENGINE = InnoDB;

");
$installer->endSetup();