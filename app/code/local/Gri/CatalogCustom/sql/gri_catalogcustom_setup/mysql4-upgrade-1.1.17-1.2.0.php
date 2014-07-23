<?php
/* @var $this Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
$installer = $this;
$installer->startSetup();

$entityTypeId = $installer->getEntityTypeId('catalog_product');
$allAttributeSetIds = $installer->getAllAttributeSetIds($entityTypeId);

$installer->addAttribute($entityTypeId, $code = 'on_sale', $definition = array(
    'label' => 'On Sale',
    'type' => 'int',
    'input' => 'boolean',
    'source' => 'eav/entity_attribute_source_boolean',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => TRUE,
    'required' => FALSE,
    'user_defined' => TRUE,
    'searchable' => FALSE,
    'filterable' => TRUE,
    'comparable' => FALSE,
    'is_visible_on_front' => TRUE,
    'visible_in_advanced_search' => FALSE,
    'used_in_product_listing' => TRUE,
    'is_used_for_promo_rules' => TRUE,
    'is_configurable' => FALSE,
    'unique' => FALSE,
))->updateAttribute($entityTypeId, $code, $definition);

// Add attribute to group for all attribute sets
foreach ($allAttributeSetIds as $attributeSetId) {
    $groupId = $installer->getAttributeGroupId($entityTypeId, $attributeSetId, 'Prices');
    $installer->addAttributeToGroup(
        $entityTypeId,
        $attributeSetId,
        $groupId,
        $code
    );
}
$onSaleId = $installer->getAttributeId($entityTypeId, 'on_sale');
$priceId = $installer->getAttributeId($entityTypeId, 'price');
$specialPriceId = $installer->getAttributeId($entityTypeId, 'special_price');
$productTable = $installer->getTable('catalog/product');
$sql = "
SELECT p.`entity_type_id`, '{$onSaleId}' `attribute_id`, p.`store_id`, p.`entity_id`, IF(p.value > sp.value , 1, 0) `value`
FROM `{$productTable}_decimal` p
JOIN `{$productTable}_decimal` sp ON sp.`entity_id` = p.`entity_id` AND sp.`attribute_id` = '{$specialPriceId}' AND sp.`store_id` = 0
WHERE p.`attribute_id` = '{$priceId}'
AND p.`store_id` = 0
AND p.`value` IS NOT NULL
AND sp.`value` IS NOT NULL
";
$data = $installer->getConnection()->fetchAll($sql);
$installer->getConnection()->insertOnDuplicate($productTable . '_int', $data);

$installer->endSetup();
