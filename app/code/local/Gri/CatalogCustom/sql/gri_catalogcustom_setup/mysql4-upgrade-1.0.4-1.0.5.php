<?php
$installer = $this;
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
$installer->startSetup();

/* @var $category Mage_Catalog_Model_Category */
$category = Mage::getModel('catalog/category');
for ($categoryId = 13; $categoryId <= 16; ++$categoryId) {
    $category->unsetData()->setStoreId(0)->load($categoryId);
    $category->setPageLayout('one_column')->save();
}

$installer->endSetup();