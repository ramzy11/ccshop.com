<?php
/* @var $this Mage_Customer_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

// Enable name prefix in address
$this->updateAttribute('customer_address', 'prefix', array('is_visible' => 1));

// System config
$groups = array(
    'address' => array(
        'fields' => array(
            'street_lines' => array(
                'value' => '1',
            ),
            'prefix_options' => array(
                'value' => 'Mr.;Ms.;Mrs.',
            ),
        ),
    ),
);

Mage::getModel('adminhtml/config_data')
    ->setSection('customer')
    ->setGroups($groups)
    ->save();

$installer->endSetup();
