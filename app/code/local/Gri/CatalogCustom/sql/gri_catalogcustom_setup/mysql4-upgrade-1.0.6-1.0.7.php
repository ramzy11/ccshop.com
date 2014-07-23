<?php
/* @var $this Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
$installer = $this;
$installer->startSetup();

$thumbnails = array(
    17 => 's1.jpg',
    37 => 's2.jpg',
    44 => 's3.jpg',
    56 => 's4.jpg',
);
/* @var $category Gri_CatalogCustom_Model_Category */
$category = Mage::getModel('catalog/category');
$category->getResource()->getAttribute('thumbnail')->setIsGlobal(1);
Mage::getDesign()->setPackageName('gri');
$skinImageDir = Mage::getBaseDir('skin') . Mage::getDesign()->getSkinUrl('images/home/');
$categoryImageDir = Mage::getBaseDir('media') . DS . 'catalog' . DS . 'category' . DS;
foreach ($thumbnails as $id => $thumbnail) {
    $category->unsetData();
    $category->setStoreId(0)->load($id);
    $source = $skinImageDir . $thumbnail;
    $destination = $categoryImageDir . $thumbnail;
    try {
        copy($source, $destination);
    }
    catch (Exception $e) {}
    $category->setThumbnail($thumbnail);
    $category->getResource()->saveAttribute($category, 'thumbnail');
}

$installer->endSetup();
