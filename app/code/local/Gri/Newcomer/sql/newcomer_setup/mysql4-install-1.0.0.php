<?php
/* @var $this Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

/* @var $block Mage_Cms_Model_Block */
$block = Mage::getModel('cms/block');
$data = array(
    array(
        'identifier' => 'newcomer_popup',
        'title' => 'Newcomer Popup',
        'is_active' => 1,
        'content' => <<<EOT
<div class="newcomer">Welcome newcomer!</div>
EOT
    ,
    ),
);
Mage::app()->reinitStores();
foreach (Mage::app()->getStores() as $storeId => $store) {
    if ($storeId <= 1) continue;
    foreach ($data as $d) {
        $block->unsetData();
        $block->setStoreId($storeId)->setLoadInactive(TRUE)->load($d['identifier']);
        $block->addData($d);
        $block->setStores(array($storeId))->save();
    }
}

$installer->endSetup();
