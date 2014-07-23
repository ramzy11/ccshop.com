<?php
/* @var $this Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$creditmemoTable = $installer->getTable('sales/creditmemo');
$creditmemoCommentTable = $installer->getTable('sales/creditmemo_comment');
if (!$installer->getConnection()->fetchOne("SHOW COLUMNS FROM `{$creditmemoTable}` LIKE 'notified'")) {
    $installer->run("ALTER TABLE `{$creditmemoTable}` ADD COLUMN `notified` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0';");
}
if (!$installer->getConnection()->fetchOne("SHOW COLUMNS FROM `{$creditmemoCommentTable}` LIKE 'type'")) {
    $installer->run("ALTER TABLE `{$creditmemoCommentTable}` ADD COLUMN `type` VARCHAR(30) NOT NULL DEFAULT '';");
}

$installer->endSetup();
