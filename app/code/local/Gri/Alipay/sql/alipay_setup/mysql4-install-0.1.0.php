<?php
$installer = $this;

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS `{$this->getTable('alipay_api_debug')}`;
CREATE TABLE `{$this->getTable('alipay_api_debug')}` (
  `debug_id` int(10) unsigned NOT NULL auto_increment,
  `debug_at` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `request_body` text,
  `response_body` text,
  PRIMARY KEY  (`debug_id`),
  KEY `debug_at` (`debug_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->addAttribute('quote_payment', 'alipay_payer_id', array());
$installer->addAttribute('quote_payment', 'alipay_payer_status', array());
$installer->addAttribute('quote_payment', 'alipay_correlation_id', array());

$installer->addAttribute('order', 'alipay_ipn_customer_notified', array('type' => 'int', 'visible' => false, 'default' => 0));

$installer->endSetup();
