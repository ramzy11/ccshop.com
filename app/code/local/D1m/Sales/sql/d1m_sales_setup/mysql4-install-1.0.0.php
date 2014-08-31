<?php
/** @var Mage_Sales_Model_Resource_Setup $installer */
$installer = $this;

$installer->startSetup();

$installer->addAttribute('quote_address', 'area_code',
    array('label' => 'Area Code',
          'visible' => true,
          'required' => false,
));
$installer->addAttribute('order_address', 'area_code',
    array('label' => 'Area Code',
        'visible' => true,
        'required' => false,
));

$installer->endSetup();