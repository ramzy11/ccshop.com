<?php
/* @var $this Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
$installer = $this;
$installer->startSetup();

$entityTypeId = $installer->getEntityTypeId('catalog_product');
$allAttributeSetIds = $installer->getAllAttributeSetIds($entityTypeId);

$installer->addAttribute($entityTypeId, 'color_label', array(
    'label' => 'Color Label',
    'type' => 'varchar',
    'input' => 'text',
    'source' => '',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible' => TRUE,
    'required' => FALSE,
    'user_defined' => TRUE,
    'searchable' => FALSE,
    'filterable' => FALSE,
    'comparable' => FALSE,
    'visible_on_front' => TRUE,
    'visible_in_advanced_search' => FALSE,
    'used_in_product_listing' => TRUE,
    'unique' => FALSE,
    'apply_to' => 'simple',
));

$colorCodeId = $installer->getAttributeId($entityTypeId, 'color_code');

// Add attribute to group for all attribute sets
foreach ($allAttributeSetIds as $attributeSetId) {
    $group = $installer->getTableRow('eav/entity_attribute', 'attribute_id', $colorCodeId, NULL,
        'attribute_set_id', $attributeSetId
    );
    $group and $installer->addAttributeToGroup(
        $entityTypeId,
        $attributeSetId,
        $group['attribute_group_id'],
        'color_label',
        $group['sort_order'] + 1
    );
}
$colorLabelId = $installer->getAttributeId($entityTypeId, 'color_label');
$productTable = $installer->getTable('catalog/product');
$attributeOptionValueTable = $installer->getTable('eav/attribute_option_value');
$sql = "
SELECT cc.`entity_type_id`, '{$colorLabelId}' `attribute_id`, cc.`store_id`, cc.`entity_id`, IFNULL(sv.`value`, dv.`value`) `value`
FROM `{$productTable}_int` cc
JOIN `{$attributeOptionValueTable}` dv ON dv.`option_id` = cc.`value` AND dv.`store_id` = 0
LEFT JOIN `{$attributeOptionValueTable}` sv ON sv.`option_id` = cc.`value` AND sv.`store_id` = 1
WHERE cc.`attribute_id` = '{$colorCodeId}' AND cc.`store_id` = 0 AND cc.`value` IS NOT NULL
";
$data = $installer->getConnection()->fetchAll($sql);
$installer->getConnection()->insertOnDuplicate($productTable . '_varchar', $data);

$installer->endSetup();
