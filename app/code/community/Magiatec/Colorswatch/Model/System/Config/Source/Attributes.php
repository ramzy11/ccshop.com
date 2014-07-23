<?php
class Magiatec_Colorswatch_Model_System_Config_Source_Attributes
{
    public function toOptionArray()
    {
        $collection = Mage::getModel('eav/entity_attribute')->getCollection();
        $collection->getSelect()->joinInner(
            array('catalog_eav_attr' => $collection->getTable('catalog/eav_attribute')),
            'main_table.attribute_id = catalog_eav_attr.attribute_id',
            array('catalog_eav_attr.is_configurable')
        );
        $collection->addFieldToFilter('catalog_eav_attr.is_configurable', 1);
        $collection->addFieldToFilter('catalog_eav_attr.is_global', 1);
        $collection->addFieldToFilter('main_table.frontend_input', 'select');
        
        $entityTypeId = Mage::getModel('eav/entity')->setType(Mage_Catalog_Model_Product::ENTITY)->getTypeId(); 
        $collection->addFieldToFilter('main_table.entity_type_id', $entityTypeId);
        
        $items = array();
        foreach ($collection as $item) {
            $items[] = array(
                'value' => $item->getAttributeId(),
                'label' => $item->getFrontendLabel(),
            );
        }
        
        return $items;
    }
}
