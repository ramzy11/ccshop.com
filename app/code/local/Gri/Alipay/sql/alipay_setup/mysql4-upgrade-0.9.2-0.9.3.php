<?php
$installer = $this;

$installer->startSetup();

$setup = new Mage_Sales_Model_Mysql4_Setup('sales_setup');


    $setup->addAttribute('quote', 'alipay_pay_method', array('type' => 'varchar'));
    $setup->addAttribute('quote', 'alipay_pay_bank', array('type' => 'varchar'));

    $setup->addAttribute('order', 'alipay_pay_method', array('type' => 'varchar'));
    $setup->addAttribute('order', 'alipay_pay_bank', array('type' => 'varchar'));
    
$installer->endSetup();