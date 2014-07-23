<?php
/* @var $installer Gri_Cms_Model_Mysql4_Setup */
$installer = $this;
$installer->startSetup();

/* @var $block Mage_Cms_Model_Block */
$block = Mage::getModel('cms/block');
$data = array(
    array(
        'identifier' => 'ninewest-shop',
        'title' => 'Ninewest Shop',
        'is_active' => 1,
        'content' => <<<EOT
<div class="banner"><img src="{{skin_url="images/ex_banner2.jpg"}}" /><p class="spacer"></p></div>
<div>{{block type="gri_catalogcustom/category_group" name="ninewest-shop" category_ids="186,61,89,95"}}</div>
EOT
    ,
    ),
    array(
        'identifier' => 'stevemadden-shop-shoes',
        'title' => 'Steve Madden Shop Shoes',
        'is_active' => 1,
        'content' => <<<EOT
<div class="banner"><img src="{{skin_url="images/ex_banner1.jpg"}}" /><p class="spacer"></p></div>
<div>{{block type="gri_catalogcustom/category_group" name="stevemadden-shop-shoes" category_ids="103,104,105,111,112,115,113"}}</div>
EOT
    ,
    ),
);
foreach ($data as $d) {
    $block->unsetData();
    $block->load($d['identifier']);
    foreach ($d as $k => $v) {
        $block->setData($k, $v);
    }
    $block->setStores(array(0))->save();
}

$installer->endSetup();
