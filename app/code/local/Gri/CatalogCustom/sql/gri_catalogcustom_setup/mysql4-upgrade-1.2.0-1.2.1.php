<?php
/* @var $this Mage_Catalog_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/* @var $productResource Mage_Catalog_Model_Resource_Product */
$productResource = Mage::getResourceModel('catalog/product');

$onSaleAttribute = $productResource->getAttribute('on_sale');
$priceAttribute = $productResource->getAttribute('price');
$specialPriceAttribute = $productResource->getAttribute('special_price');

$superLinkTable = $installer->getTable('catalog/product_super_link');

// Initialize special_price
$sql = "SELECT p.entity_type_id, p.attribute_id, p.store_id, sl.parent_id entity_id, MAX(p.value) `value`
FROM `{$superLinkTable}` sl
JOIN `{$specialPriceAttribute->getBackendTable()}` p ON p.entity_id = sl.product_id
    AND p.attribute_id = '{$specialPriceAttribute->getAttributeId()}' AND p.store_id = 0
WHERE p.value IS NOT NULL
GROUP BY sl.parent_id";
$data = $installer->getConnection()->fetchAll($sql);
$installer->getConnection()->insertOnDuplicate($specialPriceAttribute->getBackendTable(), $data);

// Initialize on_sale
$sql = "
SELECT p.`entity_type_id`, '{$onSaleAttribute->getAttributeId()}' `attribute_id`, p.`store_id`, p.`entity_id`, IF(p.value > sp.value , 1, 0) `value`
FROM `{$priceAttribute->getBackendTable()}` p
JOIN `{$specialPriceAttribute->getBackendTable()}` sp ON sp.`entity_id` = p.`entity_id` AND sp.`attribute_id` = '{$specialPriceAttribute->getAttributeId()}' AND sp.`store_id` = 0
WHERE p.`attribute_id` = '{$priceAttribute->getAttributeId()}'
AND p.`store_id` = 0
AND p.`value` IS NOT NULL
AND sp.`value` IS NOT NULL
";
$data = $installer->getConnection()->fetchAll($sql);
$installer->getConnection()->insertOnDuplicate($onSaleAttribute->getBackendTable(), $data);

$installer->endSetup();
