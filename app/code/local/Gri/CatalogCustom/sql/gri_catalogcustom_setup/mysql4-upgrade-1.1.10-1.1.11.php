<?php
    /* @var $this Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
    $installer = $this;
    $setup = new Mage_Eav_Model_Entity_Setup('core_setup');
    $installer->startSetup();

    $size_filter = Mage::getModel('eav/entity_attribute')->loadByCode('catalog_product','size_filter_1');
    $size_filter_id = $size_filter->getAttributeId();
    if($size_filter_id){
        $size_filter->setAttributeCode('size_filter');
        $size_filter->setFrontendLabel('Size Filter');
        $size_filter->save();
    }

    // delete size_filter_2
    /*@var $ins Mage_Eav_Model_Entity_Attribute  */
    $ins = Mage::getModel('eav/entity_attribute')->loadByCode('catalog_product','size_filter_2');
    $ins->getAttributeId() && $ins->delete();

    $option = array( 'values' => array( '0' => '00'),
                      'attribute_id' =>  Mage::getModel('eav/entity_attribute')->loadByCode('catalog_product','size_filter')->getAttributeId()
                    );
    $setup->addAttributeOption($option);

    $installer->endSetup();
