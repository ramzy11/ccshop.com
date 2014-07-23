<?php
/* @var $this Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
$installer = $this;
$installer->startSetup();

$entityTypeId = $installer->getEntityTypeId('catalog_product');

// Add Attribute ref_no
$installer->addAttribute($entityTypeId, 'ref_no', array(
    'label' => 'Ref. No.',
    'type' => 'text',
    'input' => 'text',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => TRUE,
    'required' => FALSE,
    'user_defined' => TRUE,
    'searchable' => FALSE,
    'filterable' => FALSE,
    'comparable' => FALSE,
    'visible_on_front' => FALSE,
    'visible_in_advanced_search' => FALSE,
    'used_in_product_listing' => TRUE,
    'unique' => FALSE,
));

// Add Attribute Limited Edition
$installer->addAttribute($entityTypeId, 'limited_edition', array(
    'label' => 'Limited Edition',
    'type' => 'int',
    'input' => 'boolean',
    'source' => 'eav/entity_attribute_source_boolean',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => TRUE,
    'required' => FALSE,
    'user_defined' => TRUE,
    'searchable' => FALSE,
    'filterable' => FALSE,
    'comparable' => FALSE,
    'visible_on_front' => FALSE,
    'visible_in_advanced_search' => FALSE,
    'used_in_product_listing' => TRUE,
    'unique' => FALSE,
));

$AttributeIds = array();
foreach(array('limited_edition', 'ref_no') as $v){
    $AttributeIds[] = Mage::getModel('eav/entity_attribute')->getIdByCode('catalog_product', $v);
}

$sql = "SELECT `attribute_set_id`, `attribute_group_id`  FROM `{$installer->getTable('eav_attribute_group')}` WHERE `attribute_group_name`='Prices'";
$group_ids = $installer->getConnection()->fetchAll($sql, PDO::FETCH_ASSOC);
foreach($AttributeIds as $attributeId){
    foreach($group_ids as $row){
        $data = array('entity_type_id' => Mage::getModel('gri_api/api_hkAs400')->getEntityTypeId('catalog_product'),
            'attribute_set_id' => $row['attribute_set_id'],
            'attribute_group_id' => $row['attribute_group_id'],
            'attribute_id' => $attributeId
        );
        try{
            Mage::log(var_export($data,true), null, 'upgrade.log');
            $installer->getConnection()->insertMultiple($installer->getTable('eav_entity_attribute'), $data);
        }catch (Exception $e){
            Mage::log($e->getMessage(),null,'exception.log');
        }
    }
}

$installer->endSetup();
