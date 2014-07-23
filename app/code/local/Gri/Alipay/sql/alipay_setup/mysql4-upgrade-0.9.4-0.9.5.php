<?php
/* @var $this Mage_Sales_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$installer->addAttribute('order', 'payment_account', array());

$installer->endSetup();
