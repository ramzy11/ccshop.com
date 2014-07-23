<?php
/* @var $this Mage_Sales_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/* @var $status Mage_Sales_Model_Order_Status */
$status = Mage::getModel('sales/order_status');
foreach (array(
    array(
        'status' => 'refunded',
        'label' => 'Refunded',
        'stat' => 'closed',
        'is_default' => 1,
    ),
    array(
        'status' => 'partial_refunded',
        'label' => 'Partial Refunded',
        'stat' => 'complete',
        'is_default' => 0,
    ),
) as $data) {
    $status->unsetData()->load($data['status']);
    $status->setData($data)->save();
    $status->assignState($data['stat'], $data['is_default']);
}

$creditmemoTable = $installer->getTable('sales/creditmemo');
$creditmemoGridTable = $installer->getTable('sales/creditmemo_grid');
if (!$installer->getConnection()->fetchOne("SHOW COLUMNS FROM `{$creditmemoTable}` LIKE 'refunded_at'")) {
    $installer->run("ALTER TABLE `{$creditmemoTable}` ADD COLUMN `refunded_at` TIMESTAMP NULL DEFAULT NULL;");
}
if (!$installer->getConnection()->fetchOne("SHOW COLUMNS FROM `{$creditmemoGridTable}` LIKE 'refunded_at'")) {
    $installer->run("ALTER TABLE `{$creditmemoGridTable}` ADD COLUMN `refunded_at` TIMESTAMP NULL DEFAULT NULL;");
}
if (!$installer->getConnection()->fetchOne("SHOW COLUMNS FROM `{$creditmemoGridTable}` LIKE 'base_subtotal'")) {
    $installer->run("ALTER TABLE `{$creditmemoGridTable}` ADD COLUMN `base_subtotal` DECIMAL(12,4) DEFAULT NULL;");
}
if (!$installer->getConnection()->fetchOne("SHOW COLUMNS FROM `{$creditmemoGridTable}` LIKE 'base_shipping_amount'")) {
    $installer->run("ALTER TABLE `{$creditmemoGridTable}` ADD COLUMN `base_shipping_amount` DECIMAL(12,4) DEFAULT NULL;");
}
if (!$installer->getConnection()->fetchOne("SHOW COLUMNS FROM `{$creditmemoGridTable}` LIKE 'base_customer_balance_total_refunded'")) {
    $installer->run("ALTER TABLE `{$creditmemoGridTable}` ADD COLUMN `base_customer_balance_total_refunded` DECIMAL(12,4) DEFAULT NULL;");
}

$installer->run("UPDATE `{$creditmemoTable}` t
JOIN `{$creditmemoGridTable}` g ON g.`entity_id` = t.`entity_id`
SET t.`refunded_at` = t.`updated_at`, g.`refunded_at` = t.`updated_at`
WHERE t.`state` IN (2, 6);
UPDATE `{$creditmemoTable}` t
JOIN `{$creditmemoGridTable}` g ON g.`entity_id` = t.`entity_id`
SET g.`base_subtotal` = t.`base_subtotal`,
g.`base_shipping_amount` = t.`base_shipping_amount`,
g.`base_customer_balance_total_refunded` = t.`base_customer_balance_total_refunded`;
");

$installer->endSetup();
