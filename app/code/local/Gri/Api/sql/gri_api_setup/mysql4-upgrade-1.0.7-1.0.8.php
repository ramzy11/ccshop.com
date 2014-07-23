<?php
/* @var $this Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$sql = "ALTER TABLE `{$this->getTable('gri_api_product')}` add `sent_at` TIMESTAMP ";

$installer->run($sql);

$installer->endSetup();
