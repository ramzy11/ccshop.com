<?php
/* @var $installer Gri_Cms_Model_Mysql4_Setup */
$installer = $this;
$installer->startSetup();

/* @var $block Mage_Cms_Model_Block */
$block = Mage::getModel('cms/block');
$data = array(
    array(
        'identifier' => 'ninewest_nav',
        'title' => 'Nine West Nav',
        'is_active' => 1,
        'content' => <<<EOT
<div class="left-static-nav">
<ul>
<li><a href="{{store_url='ninewest/shop/new-arrivals.html'}}">NEW ARRIVALS</a></li>
<li><a href="{{store_url='ninewest/shop/best-sellers.html'}}">BEST SELLERS</a></li>
<li><a href="{{store_url='ninewest/shop/editors.html'}}">EDITOR'S PICKS</a></li>
<li><a href="{{store_url='ninewest/shop/exclusives.html'}}">EXCLUSIVES</a></li>
<li><a href="{{store_url='ninewest/shop/shop-by-look.html'}}">SHOP BY LOOK</a></li>
</ul>
</div>
EOT
    ,
    ),

	array(
		'identifier' => 'eqiq_nav',
		'title' => 'EQ::IQ Nav',
		'is_active' => 1,
		'content' => <<<EOT
<div class="left-static-nav">
<ul>
<li><a href="{{store_url='eqiq/shop/new-arrivals.html'}}">NEW ARRIVALS</a></li>
<li><a href="{{store_url='eqiq/shop/best-sellers.html'}}">BEST SELLERS</a></li>
<li><a href="{{store_url='eqiq/shop/editors.html'}}">EDITOR'S PICKS</a></li>
<li><a href="{{store_url='eqiq/shop/exclusives.html'}}">EXCLUSIVES</a></li>
</ul>
</div>
EOT
		,
	),

	array(
		'identifier' => 'stevemadden_nav',
		'title' => 'Steve Madden Nav',
		'is_active' => 1,
		'content' => <<<EOT
<div class="left-static-nav">
<ul>
<li><a href="{{store_url='stevemadden/shop/new-arrivals.html'}}">NEW ARRIVALS</a></li>
<li><a href="{{store_url='stevemadden/shop/best-sellers.html'}}">BEST SELLERS</a></li>
<li><a href="{{store_url='stevemadden/shop/editors.html'}}">EDITOR'S PICKS</a></li>
<li><a href="{{store_url='stevemadden/shop/exclusives.html'}}">EXCLUSIVES</a></li>
</ul>
</div>
EOT
		,
	),

	array(
		'identifier' => 'betseyjohnson_nav',
		'title' => 'Betsey Johnson Nav',
		'is_active' => 1,
		'content' => <<<EOT
<div class="left-static-nav">
<ul>
<li><a href="{{store_url='betseyjohnson/shop/new-arrivals.html'}}">NEW ARRIVALS</a></li>
<li><a href="{{store_url='betseyjohnson/shop/best-sellers.html'}}">BEST SELLERS</a></li>
<li><a href="{{store_url='betseyjohnson/shop/editors.html'}}">EDITOR'S PICKS</a></li>
<li><a href="{{store_url='betseyjohnson/shop/exclusives.html'}}">EXCLUSIVES</a></li>
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


