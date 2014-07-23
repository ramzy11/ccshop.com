<?php
/* @var $this Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$affiliateOrderTable = $installer->getTable('gri_affiliate/order');
$installer->run("ALTER TABLE `{$affiliateOrderTable}`
DROP KEY `hash`,
ADD KEY `hash` (hash)
");

$installer->endSetup();
