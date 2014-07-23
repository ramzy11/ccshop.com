<?php
$installer = $this;

$installer->startSetup();

$installer->addAttribute('quote_payment', 'alipay_pay_method', array());
$installer->addAttribute('quote_payment', 'alipay_pay_bank', array());

$installer->endSetup();