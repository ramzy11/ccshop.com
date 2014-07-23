<?php
/* @var $installer Gri_Cms_Model_Mysql4_Setup */
$installer = $this;
$installer->startSetup();

/* @var $block Mage_Cms_Model_Block */
$block = Mage::getModel('cms/block');
$data = array(
	array(
		'identifier' => 'eqiq_bestsellers',
		'title' => 'EQ:IQ Best Sellers',
		'is_active' => 1,
		'content' => <<<EOT
<div class="eqiq_bestsellers">
<p>best sellers</p>
<a class="eqiq_bestsellers_link" href="{{store_url='eqiq/shop/best-sellers.html'}}">view all</a></div>
EOT
		,
	),

	array(
		'identifier' => 'eqiq_lookbook_viewall',
		'title' => 'eqiq lookbook viewall',
		'is_active' => 1,
		'content' => <<<EOT
<div class="eqiq_lookbook_viewall">
<p>s/s 2012 lookbook</p>
<a class="eqiq_lookbook_link" href="{{store_url='eqiq/lookbook'}}">view all</a></div>
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