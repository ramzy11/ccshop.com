<?php
/* @var $this Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
$installer = $this;
$installer->startSetup();
$archived_id = Mage::getModel('eav/entity_attribute')->getIdByCode('catalog_product', 'is_archived');

$installer->run("
         DELETE FROM `{$installer->getTable('eav_entity_attribute')}` WHERE `attribute_id`= '{$archived_id}';
 ");

//$collection = Mage::getResourceModel('eav/entity_attribute_set_collection');

$sql = "SELECT `attribute_set_id`, `attribute_group_id`  FROM `{$installer->getTable('eav_attribute_group')}` WHERE `attribute_group_name`='Images'";
$group_ids = $installer->getConnection()->fetchAll($sql, PDO::FETCH_ASSOC);
if($archived_id) {
    foreach($group_ids as $row){
        $data = array('entity_type_id' => Mage::getModel('gri_api/api_hkAs400')->getEntityTypeId('catalog_product'),
                    'attribute_set_id' => $row['attribute_set_id'],
                    'attribute_group_id' => $row['attribute_group_id'],
                    'attribute_id' => $archived_id
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
