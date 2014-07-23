<?php
/* @var $installer Gri_Cms_Model_Mysql4_Setup */
$installer = $this;
$installer->startSetup();

/* @var $block Mage_Cms_Model_Block */
$block = Mage::getModel('cms/block');
$data = array(
    array(
        'identifier' => 'cart-banner',
        'title' => 'Cart Banner',
        'is_active' => 1,
        'content' => '
<div class="cart_banner">
<a href="#"><img alt="cart banner" src="{{skin_url=\'images/cart_banner.jpg\'}}"></a>
</div>
' ,
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


