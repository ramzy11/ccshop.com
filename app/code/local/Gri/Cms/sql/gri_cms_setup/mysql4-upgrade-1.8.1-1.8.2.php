<?php
/* @var $installer Gri_Cms_Model_Mysql4_Setup */
$installer = $this;
$installer->startSetup();


/* @var $block Mage_Cms_Model_Block */
$block = Mage::getModel('cms/block');
$data = array(
    array(
        'identifier' => 'home_mid_block_one',
        'title' => 'Home Middle Block One',
        'is_active' => 1,
        'content' => <<<EOT
        <img src="{{media url="wysiwyg/Home_Banner/img_home_one.jpg"}}" alt="" />
EOT
        ,
    ),
    array(
        'identifier' => 'home_mid_block_two',
        'title' => 'Home Middle Block Two',
        'is_active' => 1,
        'content' => <<<EOT
        <img src="{{media url="wysiwyg/Home_Banner/01_home_two.jpg"}}" alt="" />
EOT
		,
    ),

    array(
        'identifier' => 'home_mid_block_three',
        'title' => 'Home Middle Block Three',
        'is_active' => 1,
        'content' => <<<EOT
        <img src="{{media url="wysiwyg/Home_Banner/img_home_three.jpg"}}" alt="" />
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
    ->setContent('
<div class="slider bot_space clearer">{{block type="interaktingslider/interaktingslider" name="interaktingslider" group="home"}}</div>
<div class="home_mid_block_list clearer">
<div class="home_mid_block one">{{block type="cms/block" block_id="home_mid_block_one" template="cms/content.phtml"}}</div>
<div class="home_mid_block two">{{block type="cms/block" block_id="home_mid_block_two" template="cms/content.phtml"}}</div>
<div class="home_mid_block three">{{block type="cms/block" block_id="home_mid_block_three" template="cms/content.phtml"}}</div>
</div>
<div class="home_mid_block_list clearer">{{block type="cms/block" block_id="shop_new_in" template="cms/content.phtml"}}</div>
<div class="home_mid_block_list clearer">{{block type="instagramconnect/homepage" template="ikantam/instagram_connect/homepage.phtml"}}</div>
')
    ->save()
;


$installer->endSetup();
