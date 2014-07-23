<?php
/* @var $this Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
$installer = $this;
$installer->startSetup();

Mage::getConfig()->reinit();
if('HK' == Mage::getConfig()->getNode('default/' . Mage_Core_Helper_Data::XML_PATH_DEFAULT_COUNTRY)->__toString()){
    $brands = array(array('identifier'=>'jeannepierre', 'title'=>'Jeanne Pierre'));

    $pageCollection = Mage::getResourceModel('cms/page_collection')
        ->addFieldToFilter('identifier',array('like'=> '%ninewest%'))
        ->load();

    /* @var $block Mage_Cms_Model_Block */
    $block = Mage::getModel('cms/block');
    foreach($brands as $brand){
        $cmsPage = Mage::getModel('cms/page');
        foreach($pageCollection as $item){
            $data = $item->load(null)->getData();
            foreach($data as $key => $val){
                if(is_string($val) && (stripos($val,'ninewest') !== false ||  stripos($val,'Nine West') !== false)){
                    $data[$key] = str_replace('ninewest', $brand['identifier'], $val);
                    $data[$key] = str_ireplace('Nine West', $brand['title'], $data[$key]);
                }
            }

            unset($data['page_id']);
            $cmsPage->setData($data);
            if(!Mage::getModel('cms/page')->checkIdentifier($data['identifier'],$data['store_id'])){
                $cmsPage->save();
            }
            $cmsPage->unsetData();
        }
    }
}

$installer->endSetup();
