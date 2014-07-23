<?php
/* Brand Landing Page Block */
/* @var $installer Gri_Cms_Model_Mysql4_Setup */
$installer = $this;
$installer->startSetup();

/* @var $block Mage_Cms_Model_Block */
$block = Mage::getModel('cms/block');
$brandUrlKeys = array (
    'eqiq',
    'ninewest',
    'anne-klein',
    'betseyjohnson',
    'carolinnaespinosa',
    'joan-david',
    'jeannepierre',
    'stevemadden'
);
$stores = array(1,2);
foreach ($brandUrlKeys as $urlKey) {
    $identifier = 'v2014_about_' . $urlKey . '_brand';

    $_block = Mage::getModel('cms/block')->load($identifier,'identifier');
    if($_block->getId()){
        $_block->delete();
    }

    foreach($stores as $storeId ) {
        $block->unsetData();
        $content = '<div class="about-brand">
    <h2>About brand</h2>
    <ul>
        <li><a href="/v2014/'.$urlKey.'/about'.'">About</a></li>
        <li><a href="/v2014/'.$urlKey.'/lookbook'.'">Look Book</a></li>
        <li><a href="/v2014/'.$urlKey.'/store">Store Locator</a></li>
    </ul></div>';

        $data = array (
            'identifier' => $identifier,
            'title' => ' V2014 About ' . ucfirst($urlKey) . ' Brand',
            'is_active' => 1,
            'content' => $content
        );

        $block->setData($data);
        $block->setStores(array($storeId))->save();
    }
}

$installer->endSetup();
