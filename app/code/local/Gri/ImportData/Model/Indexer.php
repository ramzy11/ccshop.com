<?php

class Gri_ImportData_Model_Indexer extends Mage_Index_Model_Indexer
{

    public function processEntityAction(Varien_Object $entity, $entityType, $eventType)
    {
        if (!Mage::registry('skip_entity_indexer')) parent::processEntityAction($entity, $entityType, $eventType);
        return $this;
    }
}
