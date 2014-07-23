<?php
/* @var $this Mage_Sales_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$installer->addAttribute('quote', 'fapiao', array('type'=>'varchar'));
$installer->addAttribute('quote', 'remarks', array('type'=>'varchar'));

$installer->endSetup();
