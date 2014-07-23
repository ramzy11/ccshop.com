<?php
/* @var $installer Gri_Cms_Model_Mysql4_Setup */
$installer = $this;
$installer->startSetup();

/* @var $block Mage_Cms_Model_Block */
$block = Mage::getModel('cms/block');
$data = array(
    array(
        'identifier' => 'betseyjohnson',
        'title' => 'Betseyjohnson',
        'is_active' => 1,
        'content' => '
<div class="bj_img"><img src="{{skin_url=\'images/bj_img.png\'}}" alt="bj_img" /></div>
<div class="bj_socialnetwork">
<p>STAY UP TO DATE WITH B.J.&amp; be the FIRST to know about promotion + events!</p>
<ul>
<li class="first"><a href="#"><img src="{{skin_url=\'images/facebook.png\'}}" alt="facebook" /></a></li>
<li><a href="#"><img src="{{skin_url=\'images/twitter.png\'}}" alt="twitter" /></a></li>
<li><a href="#"><img src="{{skin_url=\'images/pin.png\'}}" alt="pin" /></a></li>
<li class="last"><a href="#"><img src="{{skin_url=\'images/instagram.png\'}}" alt="instagram" /></a></li>
</ul>
</div>
<div class="bj_bottom"><img src="{{skin_url=\'images/bj_bottom.png\'}}" alt="bj_bottom" /></div>
' ,
    ),
    array(
        'identifier' => 'stevemadden',
        'title' => 'SteveMadden',
        'is_active' => 1,
        'content' => '
<div class="slider stevemadden_slider clearer">{{block type="interaktingslider/interaktingslider" name="interaktingslider" group="stevemadden"}}</div>
<div class="block_list">{{block type="cms/block" name="cms_test_block" block_id="stevemadden_block" }}</div>
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
