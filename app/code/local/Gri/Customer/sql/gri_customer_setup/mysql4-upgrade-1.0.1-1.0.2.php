<?php
/* @var $this Mage_Customer_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$installer->updateAttribute('customer_address', 'city', array(
    'is_required'  => FALSE,
));

$installer->endSetup();
