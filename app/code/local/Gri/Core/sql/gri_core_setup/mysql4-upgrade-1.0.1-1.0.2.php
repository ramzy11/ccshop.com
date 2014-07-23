<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$groups = array(
    'modules_disable_output' => array(
        'fields' => array(
            'Mage_Tag' => array(
                'value' => '1',
            ),
        ),
    ),
);

Mage::getModel('adminhtml/config_data')
    ->setSection('advanced')
    ->setGroups($groups)
    ->save();

$installer->endSetup();
