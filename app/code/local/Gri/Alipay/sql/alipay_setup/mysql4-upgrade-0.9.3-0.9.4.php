<?php
$installer = $this;

$installer->startSetup();


$installer->addAttribute('order_payment', 'alipay_pay_method', array());
$installer->addAttribute('order_payment', 'alipay_pay_bank', array());
    
$installer->endSetup();