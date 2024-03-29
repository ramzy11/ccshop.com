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
 * @package     Gri_Reward
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/gri-edition
 */

/* @var $installer Mage_Sales_Model_Mysql4_Setup */
$installer = $this;
$installer->startSetup();

$installer->run("
CREATE TABLE IF NOT EXISTS `{$installer->getTable('gri_reward/reward_salesrule')}` (
    `rule_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
    `points_delta` int(11) UNSIGNED NOT NULL DEFAULT '0',
    KEY `FK_REWARD_SALESRULE_RULE_ID` (`rule_id`),
    CONSTRAINT `FK_REWARD_SALESRULE_RULE_ID` FOREIGN KEY (`rule_id`) REFERENCES `{$installer->getTable('salesrule/rule')}` (`rule_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8
");

$installer->addAttribute('order', 'reward_salesrule_points', array('type' => 'int'));

$installer->endSetup();
