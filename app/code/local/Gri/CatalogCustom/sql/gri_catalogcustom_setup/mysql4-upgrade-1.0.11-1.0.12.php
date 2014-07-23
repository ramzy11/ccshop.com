<?php
/* @var $this Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
$installer = $this;
$installer->startSetup();

$entityTypeId = $installer->getEntityTypeId('catalog_product');
$allAttributeSetIds = $installer->getAllAttributeSetIds($entityTypeId);

$installer->addAttribute($entityTypeId, 'qty_ordered', array(
    'label' => 'Qty Ordered',
    'type' => 'decimal',
    'input' => 'text',
    'source' => '',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
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
));

// Add attribute to group for all attribute sets
foreach ($allAttributeSetIds as $attributeSetId) {
    $groupId = $installer->getAttributeGroupId($entityTypeId, $attributeSetId, 'Specific');
    $installer->addAttributeToGroup(
        $entityTypeId,
        $attributeSetId,
        $groupId,
        'qty_ordered'
    );
}

$installer->endSetup();
