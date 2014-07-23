<?php
/* @var $this Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
$installer = $this;
$installer->startSetup();

$is_archived_id = Mage::getModel('eav/entity_attribute')->getIdByCode('catalog_product','is_archived');
if($is_archived_id) {
    $installer->updateAttribute('catalog_product', 'is_archived', 'frontend_input','boolean');
    $installer->updateAttribute('catalog_product', 'is_archived' , 'source_model', 'eav/entity_attribute_source_boolean');
}

$installer->endSetup();
