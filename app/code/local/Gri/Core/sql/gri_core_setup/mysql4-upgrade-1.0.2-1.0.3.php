<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$groups = array(
    'options' => array(
        'fields' => array(
            'enabled' => array(
                'value' => '1',
            ),
            'remember_enabled' => array(
                'value' => '1',
            ),
        ),
    ),
);

Mage::getModel('adminhtml/config_data')
    ->setSection('persistent')
    ->setGroups($groups)
    ->save();

$installer->endSetup();
