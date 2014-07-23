<?php
/* @var $this Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$griReportOrderTable = $installer->getTable('gri_reports/report_order');
$orderStatusHistoryTable = $installer->getTable('sales/order_status_history');
$sql = "UPDATE (
    SELECT `parent_id`, `status`, MIN(`created_at`) `canceled_at`
    FROM `{$orderStatusHistoryTable}` sh
    WHERE `status` IN ('canceled', 'closed', 'canceled_and_refunded')
    GROUP BY `parent_id`
) c
JOIN `{$griReportOrderTable}` r ON r.`order_id` = c.`parent_id`
SET r.`canceled_at` = c.`canceled_at`";
$installer->run($sql);

$installer->endSetup();
