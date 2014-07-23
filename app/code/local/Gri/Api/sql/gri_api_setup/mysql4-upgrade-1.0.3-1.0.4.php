<?php
/* @var $this Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$sql = "ALTER TABLE `{$this->getTable('gri_api_product')}` ADD `type` CHAR(5) NOT NULL DEFAULT 'new'";

$installer->run($sql);

$installer->endSetup();
