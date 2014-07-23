<?php
/* @var $installer Gri_Cms_Model_Mysql4_Setup */
$installer = $this;
$installer->startSetup();

$blockIdentifier2CategoryIds = array(
    'shop-shoes' => 4,
    'shop-clothing' => 6,
    'shop-accessories' => 5,
);

/* @var $block Mage_Cms_Model_Block */
$block = Mage::getModel('cms/block');
/* @var $category Mage_Catalog_Model_Category */
$category = Mage::getModel('catalog/category');
$data = array(
    array(
        'identifier' => 'shop-shoes',
        'title' => 'Shop Shoes',
        'is_active' => 1,
        'content' => <<<EOT
<div>{{block type="gri_catalogcustom/category_group" name="shop-shoes" category_ids="17,24,29,35,40,41,42,43"}}</div>
EOT
    ,
    ),
    array(
        'identifier' => 'shop-clothing',
        'title' => 'Shop Clothing',
        'is_active' => 1,
        'content' => <<<EOT
<div>{{block type="gri_catalogcustom/category_group" name="shop-clothing" category_ids="60,55,56,57,58,59"}}</div>
EOT
    ,
    ),
    array(
        'identifier' => 'shop-accessories',
        'title' => 'Shop Accessories',
        'is_active' => 1,
        'content' => <<<EOT
<div>{{block type="gri_catalogcustom/category_group" name="shop-accessories" category_ids="44,45,46,47,48,49,50,51,52,53,54"}}</div>
EOT
    ,
    ),
);
$category->getResource()->getAttribute('display_mode')->setIsGlobal(1);
$category->getResource()->getAttribute('landing_page')->setIsGlobal(1);
$category->getResource()->getAttribute('available_sort_by')->setIsGlobal(1);
foreach ($data as $d) {
    $block->unsetData();
    $block->load($d['identifier']);
    foreach ($d as $k => $v) {
        $block->setData($k, $v);
    }
    $block->setStores(array(0))->save();
    $category->unsetData();
    if ($category->setStoreId(0)->load($blockIdentifier2CategoryIds[$d['identifier']])->getId()) {
        $category->setDisplayMode($category::DM_PAGE)->setLandingPage($block->getId());
        $category->save();
    }
}

$installer->endSetup();
