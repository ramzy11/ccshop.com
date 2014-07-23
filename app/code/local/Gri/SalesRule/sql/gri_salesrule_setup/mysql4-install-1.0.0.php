<?php
/* @var $this Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$salesRuleTable = $installer->getTable('salesrule/rule');
$installer->run("ALTER TABLE `{$salesRuleTable}` ADD COLUMN `condition_scope` VARCHAR(30) NOT NULL DEFAULT '';");

$installer->endSetup();
