<?php
/* @var $installer Gri_Cms_Model_Mysql4_Setup */
$installer = $this;
$installer->startSetup();

/* @var $block Mage_Cms_Model_Block */
$block = Mage::getModel('cms/block');
$data = array(
    array(
        'identifier' => 'home_top_left',
        'title' => 'Home Top Left',
        'is_active' => 1,
        'content' => '<img src="{{skin_url=\'images/home/block1.jpg\'}}" alt="01" width="470" height="360" />
        <p><a class="view_all_btn" href="#">view all</a></p>',
    ),
    array(
        'identifier' => 'home_top_right',
        'title' => 'Home Top Right',
        'is_active' => 1,
        'content' => '<img src="{{skin_url=\'images/home/block3.jpg\'}}" alt="02" width="470" height="360" />
        <p><a class="view_all_btn" href="#">view all</a></p>',
    ),
    array(
        'identifier' => 'home_bottom_left',
        'title' => 'Home Bottom Left',
        'is_active' => 1,
        'content' => '<a href="#"><img src="{{skin_url=\'images/home/left_box.jpg\'}}" alt="left_img" width="295" height="670" border="0" /></a>',
    ),
    array(
        'identifier' => 'home_center',
        'title' => 'Home Center',
        'is_active' => 1,
        'content' => '<iframe src="http://www.youtube.com/embed/UkAcscL6BVM?feature=player_detailpage" frameborder="0" width="275" height="175"></iframe>
                <div class="video_tit">LATEST INSTORE</div>
                <p>Central/Central Summer<br /> Fashion Pool Party, July 2012</p>
                <a href="#">watch highlights <img src="{{skin_url=\'images/home/arrow.jpg\'}}" alt="" width="8" height="8" border="0" /></a>',
    ),
    array(
        'identifier' => 'home_middle_right',
        'title' => 'Home Middle Right',
        'is_active' => 1,
        'content' => '<img src="{{skin_url=\'images/home/block2.jpg\'}}" alt="" width="365" height="330" />
                <p><a class="view_all_btn" href="#">view all</a></p>',
    ),
    array(
        'identifier' => 'home_bottom_banner_1',
        'title' => 'Home Bottom Banner 1',
        'is_active' => 1,
        'content' => '<a href="#"><img src="{{skin_url=\'images/home/b1.jpg\'}}" alt="ban" width="305" height="140" border="0"/></a>',
    ),
    array(
        'identifier' => 'home_bottom_banner_2',
        'title' => 'Home Bottom Banner 2',
        'is_active' => 1,
        'content' => '<a href="#"><img src="{{skin_url=\'images/home/b2.jpg\'}}" alt="ban2" width="305" height="140" border="0"/></a>',
    ),
    array(
        'identifier' => 'home_bottom_banner_3',
        'title' => 'Home Bottom Banner 3',
        'is_active' => 1,
        'content' => '<a href="#"><img src="{{skin_url=\'images/home/vip_banner.jpg\'}}" alt="ban3" width="305" height="140" border="0"/></a>',
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
        <div class="mid_rig_bot">{{block type="gri_catalogcustom/home_shopNow" name="home_shop_now"}}</div>
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
