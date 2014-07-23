<?php
/* @var $installer Gri_Cms_Model_Mysql4_Setup */
$installer = $this;
$installer->startSetup();

/* @var $block Mage_Cms_Model_Block */
$block = Mage::getModel('cms/block');
foreach (array(29, 30, 31, 33, 34, 35) as $blockId) {
    $block->unsetData()->load($blockId)->delete();
}

$installer->endSetup();
