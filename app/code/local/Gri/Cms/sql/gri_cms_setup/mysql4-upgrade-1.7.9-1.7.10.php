<?php
/* @var $installer Gri_Cms_Model_Mysql4_Setup */
$installer = $this;
$installer->startSetup();

$blockIdentifier2CategoryIds = array(
    'ninewest-shop' => 260,
    'ninewest-shop-shoes' => 61,
    'ninewest-shop-clothing' => 95,
    'ninewest-shop-accessories' => 89,

    'stevemadden-shop' => 261,
    'stevemadden-shop-shoes' => 102,
    'stevemadden-shop-accessories' => 116,

    'betseyjohnson-shop' => 263,
    'betseyjohnson-shop-shoes' => 126,
    'betseyjohnson-shop-clothing' => 140,
    'betseyjohnson-shop-accessories' => 133,

    'eqiq-shop' => 262,
    'eqiq-shop-shoes' => 194,
    'eqiq-shop-clothing' => 251,
    'eqiq-shop-accessories' => 195,
);

/* @var $block Mage_Cms_Model_Block */
$block = Mage::getModel('cms/block');
/* @var $category Mage_Catalog_Model_Category */
$category = Mage::getModel('catalog/category');
$data = array(
    // Ninewest: Brand > Shop
    array(
        'identifier' => 'ninewest-shop',
        'title' => 'Ninewest Shop',
        'is_active' => 1,
        'content' => <<<EOT
<div>{{block type="gri_catalogcustom/category_group" name="ninewest-shop" category_ids="186,61,89,95"}}</div>
EOT
    ,
    ),
    array(
        'identifier' => 'ninewest-shop-shoes',
        'title' => 'Ninewest Shop Shoes',
        'is_active' => 1,
        'content' => <<<EOT
<div>{{block type="gri_catalogcustom/category_group" name="ninewest-shop-shoes" category_ids="62,69,74,80,85,87,88"}}</div>
EOT
    ,
    ),
    array(
        'identifier' => 'ninewest-shop-clothing',
        'title' => 'Ninewest Shop Clothing',
        'is_active' => 1,
        'content' => <<<EOT
<div>{{block type="gri_catalogcustom/category_group" name="ninewest-shop-clothing" category_ids="96,97,98,99,100,101"}}</div>
EOT
    ,
    ),
    array(
        'identifier' => 'ninewest-shop-accessories',
        'title' => 'Ninewest Shop Accessories',
        'is_active' => 1,
        'content' => <<<EOT
<div>{{block type="gri_catalogcustom/category_group" name="ninewest-shop-accessories" category_ids="90"}}</div>
EOT
    ,
    ),

    // Steve Madden: Brand > Shop
    array(
        'identifier' => 'stevemadden-shop',
        'title' => 'Steve Madden Shop',
        'is_active' => 1,
        'content' => <<<EOT
<div>{{block type="gri_catalogcustom/category_group" name="stevemadden-shop" category_ids="102,116"}}</div>
EOT
    ,
    ),
    array(
        'identifier' => 'stevemadden-shop-shoes',
        'title' => 'Steve Madden Shop Shoes',
        'is_active' => 1,
        'content' => <<<EOT
<div>{{block type="gri_catalogcustom/category_group" name="stevemadden-shop-shoes" category_ids="103,104,105,111,112,115,113"}}</div>
EOT
    ,
    ),
    // No clothing for Steve Madden
    /*array(
        'identifier' => 'stevemadden-shop-clothing',
        'title' => 'Steve Madden Shop Clothing',
        'is_active' => 1,
        'content' => <<<EOT
EOT
    ,
    ),*/
    array(
        'identifier' => 'stevemadden-shop-accessories',
        'title' => 'Steve Madden Shop Accessories',
        'is_active' => 1,
        'content' => <<<EOT
<div>{{block type="gri_catalogcustom/category_group" name="stevemadden-shop-accessories" category_ids="117"}}</div>
EOT
    ,
    ),

    // Betsey Johnson: Brand > Shop
    array(
        'identifier' => 'betseyjohnson-shop',
        'title' => 'Betsey Johnson Shop',
        'is_active' => 1,
        'content' => <<<EOT
<div>{{block type="gri_catalogcustom/category_group" name="betseyjohnson-shop" category_ids="126,140,133"}}</div>
EOT
    ,
    ),
    array(
        'identifier' => 'betseyjohnson-shop-shoes',
        'title' => 'Betsey Johnson Shop Shoes',
        'is_active' => 1,
        'content' => <<<EOT
<div>{{block type="gri_catalogcustom/category_group" name="betseyjohnson-shop-shoes" category_ids="127,128,129,130,131,132"}}</div>
EOT
    ,
    ),
    array(
        'identifier' => 'betseyjohnson-shop-clothing',
        'title' => 'Betsey Johnson Shop Clothing',
        'is_active' => 1,
        'content' => <<<EOT
<div>{{block type="gri_catalogcustom/category_group" name="betseyjohnson-shop-clothing" category_ids="141,142,143,145"}}</div>
EOT
    ,
    ),
    array(
        'identifier' => 'betseyjohnson-shop-accessories',
        'title' => 'Betsey Johnson Shop Accessories',
        'is_active' => 1,
        'content' => <<<EOT
<div>{{block type="gri_catalogcustom/category_group" name="betseyjohnson-shop-accessories" category_ids="134,136,135,137,138,139"}}</div>
EOT
    ,
    ),

    // EQ:IQ: Brand > Shop
    array(
        'identifier' => 'eqiq-shop',
        'title' => 'EQ:IQ Shop',
        'is_active' => 1,
        'content' => <<<EOT
<div>{{block type="gri_catalogcustom/category_group" name="eqiq-shop" category_ids="194,251,195"}}</div>
EOT
    ,
    ),
    array(
        'identifier' => 'eqiq-shop-shoes',
        'title' => 'EQ:IQ Shop Shoes',
        'is_active' => 1,
        'content' => <<<EOT
<div>{{block type="gri_catalogcustom/category_group" name="eqiq-shop-shoes" category_ids="222,223,224,225,226,228"}}</div>
EOT
    ,
    ),
    array(
        'identifier' => 'eqiq-shop-clothing',
        'title' => 'EQ:IQ Shop Clothing',
        'is_active' => 1,
        'content' => <<<EOT
<div>{{block type="gri_catalogcustom/category_group" name="eqiq-shop-clothing" category_ids="252,253,254,255,256,257"}}</div>
EOT
    ,
    ),
    array(
        'identifier' => 'eqiq-shop-accessories',
        'title' => 'EQ:IQ Shop Accessories',
        'is_active' => 1,
        'content' => <<<EOT
<div>{{block type="gri_catalogcustom/category_group" name="eqiq-shop-accessories" category_ids="250"}}</div>
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
