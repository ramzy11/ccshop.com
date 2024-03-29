<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
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
 * @package     Gri_GiftCardAccount
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

$installer = $this;
/* @var $installer Gri_GiftCardAccount_Model_Mysql4_Setup */

$tableGCA     = $installer->getTable('gri_giftcardaccount/giftcardaccount');
$tableWebsite = $installer->getTable('core/website');

// drop orphan GCAs, modify website_id field to make it compatible with foreign key
$installer->run("DELETE FROM {$tableGCA} WHERE website_id NOT IN (SELECT website_id FROM {$tableWebsite})");
$installer->getConnection()->changeColumn($tableGCA, 'website_id', 'website_id',
    'smallint(5) UNSIGNED NOT NULL DEFAULT 0'
);
$installer->getConnection()->addConstraint('FK_GIFTCARDACCOUNT_WEBSITE_ID', $tableGCA, 'website_id',
    $tableWebsite, 'website_id', 'CASCADE', 'CASCADE'
);
