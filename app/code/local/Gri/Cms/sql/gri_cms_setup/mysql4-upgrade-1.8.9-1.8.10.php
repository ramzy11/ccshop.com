<?php
/* @var $installer Gri_Cms_Model_Mysql4_Setup */
$installer = $this;
$installer->startSetup();

/* @var $block Mage_Cms_Model_Block */
$block = Mage::getModel('cms/block');

$data = array(
    array(
        'identifier' => 'most_recent_views',
        'title' => 'Most Recent View',
        'is_active' => 1,
        'content' => <<<EOT
{{block type="gri_catalogcustom/home_mostRecentViews" block_id="most_recent_view" &nbsp; template="catalog/home/most_recent_views.phtml"}}
EOT
    ),

    array(
        'identifier' => 'shop_new_in',
        'title' => 'Shop New In',
        'is_active' => 1,
        'content' => <<<EOT
{{block type="gri_catalogcustom/home_shopNewIn" block_id="shop_new_in" &nbsp; template="catalog/home/shop_new_in.phtml"}}
EOT
    )
);
foreach ($data as $_data) {
    $block->unsetData()->load($_data['identifier'], 'identifier');
    if (!$block->getId()) {
        $block->setIdentifier($_data['identifier']);
        $block->setTitle($_data['title']);
        $block->setStores(array(0));
    }

    $block->setContent($_data['content']);
    $block->save();
}

/* @var $page Mage_Cms_Model_Page */
$page = Mage::getModel('cms/page')->load('home');
$homeContent = <<<EOT
<div class="slider bot_space clearer">{{block type="interaktingslider/interaktingslider" name="interaktingslider" group="home"}}</div>
<div class="bot_space clearer">{{block type="cms/block" block_id="replace_slideshow" template="cms/content.phtml"}}</div>
<div class="home_mid_block_list clearer">
<div class="home_mid_block one">{{block type="cms/block" block_id="home_mid_block_one" template="cms/content.phtml"}}</div>
<div class="home_mid_block two">{{block type="cms/block" block_id="home_mid_block_two" template="cms/content.phtml"}}</div>
<div class="home_mid_block three">{{block type="cms/block" block_id="home_mid_block_three" template="cms/content.phtml"}}</div>
</div>
<div class="home_mid_block_list clearer">{{block type="cms/block" block_id="shop_new_in" template="cms/content.phtml"}}</div>
<div class="home_mid_block_list clearer">{{block type="cms/block" block_id="most_recent_views" template="cms/content.phtml"}}</div>
<div class="home_mid_block_list clearer">{{block type="instagramconnect/homepage" template="ikantam/instagram_connect/homepage.phtml"}}</div>
EOT;
$page->setContent($homeContent);
$page->save();


$installer->endSetup();
