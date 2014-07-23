<?php
/* @var $this Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

/* @var $status Mage_Sales_Model_Order_Status */
$status = Mage::getModel('sales/order_status');
foreach(array(
    array(
        'status'     => 'complete',
        'label'      => 'Complete',
        'stat'       => 'complete',
        'is_default' => 1,
    ),
) as $data) {
    $status->unsetData()->load($data['status']);
    $status->setData($data)->save();
    $status->assignState($data['stat'], $data['is_default']);
}

$installer->endSetup();
