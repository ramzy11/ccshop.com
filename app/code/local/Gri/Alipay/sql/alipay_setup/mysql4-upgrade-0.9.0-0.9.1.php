<?php
$installer = $this;

$installer->startSetup();

$installer->addAttribute('customer', 'default_bank', array(
    'label'        => 'Default Alipay Bank',
    'visible'      => false,
    'required'     => false,
    'type'         => 'varchar',
    'input'        => 'text',
));

$installer->endSetup();