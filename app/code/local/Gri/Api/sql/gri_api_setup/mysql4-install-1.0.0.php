<?php
/* @var $this Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

/* @var $status Mage_Sales_Model_Order_Status */
$status = Mage::getModel('sales/order_status');
foreach(array(
    array(
        'status'     => 'notified',
        'label'      => 'Notified',
        'stat'       => 'processing',
        'is_default' => 0,
    ),
    array(
        'status'     => 'shipped',
        'label'      => 'Shipped',
        'stat'       => 'complete',
        'is_default' => 1,
    ),
    array(
        'status'     => 'return_pending',
        'label'      => 'Return Pending',
        'stat'       => 'closed',
        'is_default' => 0,
    ),
    array(
        'status'     => 'return_received',
        'label'      => 'Return Received',
        'stat'       => 'complete',
        'is_default' => 0,
    ),
    array(
        'status'     => 'exchange_complete',
        'label'      => 'Exchange Complete',
        'stat'       => 'complete',
        'is_default' => 0,
    ),
) as $data) {
    $status->unsetData()->load($data['status']);
    $status->setData($data)->save();
    $status->assignState($data['stat'], $data['is_default']);
}

$installer->endSetup();
