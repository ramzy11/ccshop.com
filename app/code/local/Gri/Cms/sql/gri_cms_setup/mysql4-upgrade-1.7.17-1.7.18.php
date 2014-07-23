<?php
/* @var $installer Gri_Cms_Model_Mysql4_Setup */
$installer = $this;
$installer->startSetup();

/* @var $block Mage_Cms_Model_Block */
$block = Mage::getModel('cms/block');
$data = array(
    array(
        'identifier' => 'home_shop_now',
        'title' => 'Home Shop Now',
        'is_active' => 1,
        'content' => <<<EOT
<div>
<p class="shop-now" align="center">SHOP NOW!</p>
{{block type="gri_catalogcustom/home_shopNow" name="home_shop_now" category_ids="17,37,44,56" labels="HEELS,WEDGES,CLUTCHES,JACKETS"}}
</div>
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

/* @var $home Mage_Cms_Model_Page */
$home = Mage::getModel('cms/page');
$home->load('home');
$home->setRootTemplate('one_column')
    ->setContent('<div class="slider bot_space clearer">{{block type="interaktingslider/interaktingslider" name="interaktingslider" group="home"}}</div>
<div class="main_top_area bot_space clearer">
    <div class="look_box">{{block type="cms/block" block_id="home_top_left" template="cms/content.phtml"}}</div>
    <div class="style_box">{{block type="cms/block" block_id="home_top_right" template="cms/content.phtml"}}</div>
</div>
<div class="main_mid_area bot_space clearer">
    <div class="mid_left_box">{{block type="cms/block" block_id="home_bottom_left" template="cms/content.phtml"}}</div>
    <div class="mid_right_box">
        <div class="mid_rig_top">
            <div class="video_area">{{block type="cms/block" block_id="home_center" template="cms/content.phtml"}}</div>
            <div class="edit_pick">{{block type="cms/block" block_id="home_middle_right" template="cms/content.phtml"}}</div>
        </div>
        <div class="mid_rig_bot">{{block type="cms/block" block_id="home_shop_now" template="cms/content.phtml"}}</div>
    </div>
</div>
<div class="main_bot_area clearer">
    <div class="bot_banner rig_space">{{block type="cms/block" block_id="home_bottom_banner_1" template="cms/content.phtml"}}</div>
    <div class="bot_banner rig_space">{{block type="cms/block" block_id="home_bottom_banner_2" template="cms/content.phtml"}}</div>
    <div class="bot_banner">{{block type="cms/block" block_id="home_bottom_banner_3" template="cms/content.phtml"}}</div>
</div>')
    ->save()
;

$installer->endSetup();
