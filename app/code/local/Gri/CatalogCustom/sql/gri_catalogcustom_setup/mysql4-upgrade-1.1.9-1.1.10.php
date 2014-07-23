<?php
/* @var $this Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
$installer = $this;
$installer->startSetup();

// Initialization: Set is_archived = 0 if not set
$entityTypeId = $installer->getEntityTypeId('catalog_product');
$attributeId = $installer->getAttributeId($entityTypeId, 'is_archived');
$productTable = $installer->getTable('catalog/product');
$select = $installer->getConnection()->select();
$select->from(array('e' => $productTable), array(
    new Zend_Db_Expr($entityTypeId),
    new Zend_Db_Expr($attributeId),
    new Zend_Db_Expr(0),
    'entity_id' => 'entity_id',
    new Zend_Db_Expr(0),
));
$select->insertIgnoreFromSelect($productTable . '_int', array(
    'entity_type_id',
    'attribute_id',
    'store_id',
    'entity_id',
    'value',
));

$installer->endSetup();
