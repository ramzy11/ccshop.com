<?php
/* @var $installer Gri_Cms_Model_Mysql4_Setup */
$installer = $this;
$installer->startSetup();

/* @var $block Mage_Cms_Model_Block */
$block = Mage::getModel('cms/block');
$data = array(
    array(
        'identifier' => 'need_help',
        'title' => 'need help',
        'is_active' => 1,
        'content' => '
<p><img src="{{skin_url=\'images/cart_q.jpg\'}}" alt="Help" width="24" height="34" /> <span class="up_c">need help?</span> Call +852 2480 8010</p>
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


