<?php
/* @var $installer Gri_Cms_Model_Mysql4_Setup */
$installer = $this;
$installer->startSetup();


/* @var $block Mage_Cms_Model_Block */
$block = Mage::getModel('cms/block');
$data = array(
    array(
        'identifier' => 'clothing_banner',
        'title' => 'Clothing Shop Banner',
        'is_active' => 1,
        'content' => <<<EOT
<div class="shop-banner border-bottom">
<div class="shop-image"><a href="/clothing.html"><img src="{{media url="wysiwyg/Clothing/Clothing_Title.jpg"}}" alt="banner" /></a></div>
</div>
EOT
        ,
    ),
    array(
        'identifier' => 'shoes_banner',
        'title' => 'Shoes Shop Banner',
        'is_active' => 1,
        'content' => <<<EOT
<div class="shop-banner border-bottom">
<div class="shop-image"><a href="/shoes.html"><img src="{{media url="wysiwyg/Shoes/Shoes_Title.jpg"}}" alt="banner" /></a></div>
</div>
EOT
		,
    ),

    array(
        'identifier' => 'accessories_banner',
        'title' => 'Accessories Shop Banner',
        'is_active' => 1,
        'content' => <<<EOT
<div class="shop-banner border-bottom">
<div class="shop-image"><a href="/accessories.html"><img src="{{media url="wysiwyg/Accessories/Accessories_Title.jpg"}}" alt="banner" /></a></div>
</div>
EOT
        ,
    ),

    array(
        'identifier' => 'bags_banner',
        'title' => 'Bags Shop Banner',
        'is_active' => 1,
        'content' => <<<EOT
<div class="shop-banner border-bottom">
<div class="shop-image"><a href="/bags.html"><img src="{{media url="wysiwyg/Bags/Bags_Title.jpg"}}" alt="banner" /></a></div>
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
