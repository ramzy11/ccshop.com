<?php
    /* @var $this Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
    $installer = $this;
    $installer->startSetup();

    // add  attribute set bag
    $accessoriesSet = Mage::getModel('eav/entity_attribute_set')->load('Accessories', 'attribute_set_name');
    $bagSet = Mage::getModel('eav/entity_attribute_set')->load('Bags', 'attribute_set_name');

    if(!$bagSet->getData('attribute_set_id')){
        $bagSet = clone $accessoriesSet;
        $bagSet->setData('attribute_set_name','Bags');
        $bagSet->unsetData('attribute_set_id');
        $bagSet->save();
    }

    // copy group of accessories
    /* @var $groupCollection Mage_Eav_Model_Resource_Entity_Attribute_Group_Collection */
    $groupCollection = Mage::getModel('eav/entity_attribute_group')->getResourceCollection()
        ->addFieldToFilter('attribute_set_id',$accessoriesSet->getAttributeSetId())
        ->load();

    $groupAttributesIds = array();
    foreach($groupCollection as $group){
        $item = $group->getData();
        $bagGroup = Mage::getModel('eav/entity_attribute_group') ;
        $item['attribute_set_id']= $bagSet->getAttributeSetId();
        strtolower($item['attribute_group_name']) == 'accessories' && $item['attribute_group_name'] = 'Bags';
        unset($item['attribute_group_id']);
        $bagGroup->setData($item);
        $bagGroup->save();

        $groupAttributesIds[] = array('accessories_attribute_group_id' => $group->getAttributeGroupId(),
            'bags_attribute_group_id' => $bagGroup->getAttributeGroupId(),
        );
    }
Mage::log(var_export($groupAttributesIds,true), null, 'upgrade.log' );

    $sql = "SELECT `entity_type_id`, `attribute_id`, `sort_order`,`attribute_group_id` FROM ".$installer->getTable('eav_entity_attribute')." WHERE `attribute_set_id`='".$accessoriesSet->getAttributeSetId()."'";
    $entityAttributeCollection = $installer->getConnection()->fetchAll($sql ,PDO::FETCH_ASSOC);
    foreach($entityAttributeCollection as $item){
        $newItem = Mage::getModel('eav/entity_attribute') ;
        $groupId = 0;
        foreach($groupAttributesIds as $groupAttribute){
            if($groupAttribute['accessories_attribute_group_id'] == $item['attribute_group_id']){
                $groupId = $groupAttribute['bags_attribute_group_id'];
                break;
            }
        }

        $item['attribute_set_id'] = $bagSet->getAttributeSetId();
        $item['attribute_group_id'] = $groupId;

        $newItem->setData($item);
        $newItem->save();
    }

    $installer->endSetup();
