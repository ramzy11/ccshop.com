<?php
/* @var $this Mage_Sales_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/* @var $status Mage_Sales_Model_Order_Status */
$status = Mage::getModel('sales/order_status');
foreach(array(
    array(
        'status'     => 'canceled_and_refunded',
        'label'      => 'Canceled and Refunded',
        'stat'       => 'closed',
        'is_default' => 0,
    ),
) as $data) {
    $status->unsetData()->load($data['status']);
    $status->setData($data)->save();
    $status->assignState($data['stat'], $data['is_default']);
}

$installer->endSetup();
