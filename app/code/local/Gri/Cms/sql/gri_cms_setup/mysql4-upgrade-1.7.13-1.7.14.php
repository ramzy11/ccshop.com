<?php
/* @var $installer Gri_Cms_Model_Mysql4_Setup */
$installer = $this;
$installer->startSetup();

/* @var $block Mage_Cms_Model_Block */
$block = Mage::getModel('cms/block');
$data = array(
	array(
		'identifier' => 'shoes_nav',
		'title' => 'Shoes Nav',
		'is_active' => 1,
		'content' => <<<EOT
<div class="left-static-nav">
<ul>
<li><a href="{{store_url='shoes/new-arrivals.html'}}">NEW ARRIVALS</a></li>
<li><a href="{{store_url='shoes/best-sellers.html'}}">BEST SELLERS</a></li>
<li><a href="{{store_url='shoes/editor.html'}}">EDITOR'S PICKS</a></li>
<li><a href="{{store_url='shoes/exclusives.html'}}">EXCLUSIVES</a></li>
</ul>
</div>
EOT
		,
	),

	array(
		'identifier' => 'accessories_nav',
		'title' => 'Accessories Nav',
		'is_active' => 1,
		'content' => <<<EOT
<div class="left-static-nav">
<ul>
<li><a href="{{store_url='accessories/new-arrivals.html'}}">NEW ARRIVALS</a></li>
<li><a href="{{store_url='accessories/best-sellers.html'}}">BEST SELLERS</a></li>
<li><a href="{{store_url='accessories/editor.html'}}">EDITOR'S PICKS</a></li>
<li><a href="{{store_url='accessories/exclusives.html'}}">EXCLUSIVES</a></li>
</ul>
</div>
EOT
		,
	),

	array(
		'identifier' => 'clothing_nav',
		'title' => 'Clothing Nav',
		'is_active' => 1,
		'content' => <<<EOT
<div class="left-static-nav">
<ul>
<li><a href="{{store_url='clothing/new-arrivals.html'}}">NEW ARRIVALS</a></li>
<li><a href="{{store_url='clothing/best-sellers.html'}}">BEST SELLERS</a></li>
<li><a href="{{store_url='clothing/editor.html'}}">EDITOR'S PICKS</a></li>
<li><a href="{{store_url='clothing/exclusives.html'}}">EXCLUSIVES</a></li>
</ul>
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

$installer->endSetup();


