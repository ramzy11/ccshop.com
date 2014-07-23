<?php
/* @var $this Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$griReportOrderTable = $installer->getTable('gri_reports/report_order');
$griReportOrderItemTable = $installer->getTable('gri_reports/report_order_item');
$installer->run("
ALTER TABLE `{$griReportOrderItemTable}` DROP COLUMN `payment_account`,
CHANGE `product_created_at` `product_created_at` TIMESTAMP NULL DEFAULT NULL;
ALTER TABLE `{$griReportOrderTable}` ADD COLUMN `carrier` VARCHAR(255) NOT NULL DEFAULT '',
ADD COLUMN `track_number` VARCHAR(255) NOT NULL DEFAULT '';
");
/* @var $orderReportModel Gri_Reports_Model_Report_Order */
$orderReportModel = Mage::getSingleton('gri_reports/report_order');
$orders = Mage::getModel('sales/order')->getCollection();
/* @var $order Gri_Sales_Model_Order */
foreach ($orders as $order) {
    $orderReportModel->orderPlacement($order)
        ->orderShipment($order)
        ->orderPayment($order);
    foreach ($order->getCreditmemosCollection() as $creditmemo) {
        $orderReportModel->orderCancellation($creditmemo);
    }
}

$installer->endSetup();
