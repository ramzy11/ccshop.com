<?php
/* @var $this Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$flashSaleProductTable = $installer->getTable('gri_flashsale/flashsale_product');
$flashSaleProductOrderedTable = $installer->getTable('gri_flashsale/flashsale_product_ordered');

$installer->run("UPDATE `{$flashSaleProductOrderedTable}` o
JOIN `{$flashSaleProductTable}` f ON f.`flash_sale_id`=o.`flash_sale_id` AND f.`product_id`=o.`product_id`
SET o.`color_code`=f.`color_code`;

UPDATE `{$flashSaleProductOrderedTable}` o
JOIN (
    SELECT `flash_sale_id`, `parent_id`, `color_code`, SUM(`qty_ordered`) `color_qty_ordered`
    FROM `{$flashSaleProductOrderedTable}`
    GROUP BY `flash_sale_id`, `parent_id`, `color_code`
) o2 ON o2.`flash_sale_id`=o.`flash_sale_id` AND o2.`parent_id`=o.`parent_id` AND o2.`color_code` = o.`color_code`
SET o.`color_qty_ordered` = o2.`color_qty_ordered`
WHERE o.`color_code` IS NOT NULL
");

$installer->endSetup();
