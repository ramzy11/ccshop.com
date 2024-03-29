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
 * @package     Gri_Cms
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/gri-edition
 */


/* @var $installer Gri_Cms_Model_Mysql4_Setup */
$installer = $this;
$installer->startSetup();

$_hierarchyMetadata = $installer->getTable('gri_cms/hierarchy_metadata');

$installer->getConnection()->addColumn($_hierarchyMetadata, 'pager_visibility', 'tinyint(4) unsigned NOT NULL');
$installer->getConnection()->addColumn($_hierarchyMetadata, 'pager_frame', 'smallint(6) unsigned NOT NULL');
$installer->getConnection()->addColumn($_hierarchyMetadata, 'pager_jump', 'smallint(6) unsigned NOT NULL');

$installer->getConnection()->addColumn($_hierarchyMetadata, 'menu_visibility', 'tinyint(4) unsigned NOT NULL');
$installer->getConnection()->addColumn($_hierarchyMetadata, 'menu_levels_up', 'tinyint(4) unsigned NOT NULL');
$installer->getConnection()->addColumn($_hierarchyMetadata, 'menu_levels_down', 'tinyint(4) unsigned NOT NULL');
$installer->getConnection()->addColumn($_hierarchyMetadata, 'menu_ordered', 'tinyint(4) unsigned NOT NULL');
$installer->getConnection()->addColumn($_hierarchyMetadata, 'menu_list_type', "varchar(50) NOT NULL default ''");

$installer->endSetup();
