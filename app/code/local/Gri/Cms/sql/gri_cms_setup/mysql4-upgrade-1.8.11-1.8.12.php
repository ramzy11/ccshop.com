<?php
/* @var $installer Gri_Cms_Model_Mysql4_Setup */
$installer = $this;
$installer->startSetup();

/* @var $page Mage_Cms_Model_Page */
$page = Mage::getSingleton('cms/page');

//8 brands
$brandUrlKeys = array (
                   'ninewest',
                   'eqiq',
                   'betseyjohnson',
                   'joan-david',
                   'carolinnaespinosa',
                   'jeannepierre',
                   'anne-klein',
                   'stevemadden');

//1: Chinese Store 2: English Store
$stores = array(1,2);

$blocks = array('lookbook','about','store');
foreach($brandUrlKeys as $url_key){

    foreach($blocks as $block) {
        foreach($stores as $storeId){
            $page->unsetData();
            $identifier = 'v2014'.'/'.$url_key.'/'.$block;
            $page->setTitle('V2014 '.ucfirst($url_key).' '.ucfirst($block));
            $page->setIdentifier($identifier);
            $page->setContent(ucfirst($url_key).' '.ucfirst($block));
            $page->setStores(array($storeId));
            $page->save();
        }
    }
}


// Store Locator
foreach($stores as $storeId){
   $page->unsetData();
   $page->setTitle('V2014 Store Locator');
   $page->setContent('V2014 Store Locator');
   $page->setIdentifier('v2014-store-locator');
   $page->setStores(array($storeId));
   $page->save();
}

$installer->endSetup();
