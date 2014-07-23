<?php
/* @var $this Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
$installer = $this;
$installer->startSetup();

$entityTypeId = $installer->getEntityTypeId('catalog_product');

// Add Attribute ref_no
$installer->addAttribute($entityTypeId, 'additional_accessories', array(
    'label' => 'Additional Accessories',
    'type' => 'text',
    'input' => 'text',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
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
foreach(array('additional_accessories') as $v){
    $AttributeIds[] = Mage::getModel('eav/entity_attribute')->getIdByCode('catalog_product', $v);
}

/* @var $productParser Mage_Catalog_Model_Convert_Parser_Product */
$ClothingSetId = Mage::getModel('catalog/convert_parser_product')->getAttributeSetId(4,'Clothing');
$BagsSetId = Mage::getModel('catalog/convert_parser_product')->getAttributeSetId(4,'Bags');
$AccessoriesSetId = Mage::getModel('catalog/convert_parser_product')->getAttributeSetId(4,'Accessories');

$sql = "SELECT `attribute_set_id`, `attribute_group_id`  FROM `{$installer->getTable('eav_attribute_group')}` WHERE `attribute_group_name`='Images' AND `attribute_set_id` in({$ClothingSetId},{$BagsSetId},{$AccessoriesSetId}) ";
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
