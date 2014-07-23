<?php
/* @var $installer Gri_Cms_Model_Mysql4_Setup */
$installer = $this;
$installer->startSetup();

/* @var $block Mage_Cms_Model_Block */
$block = Mage::getModel('cms/block');
$data = array(
    array(
        'identifier' => 'stevemadden_block',
        'title' => 'Steve Madden Block',
        'is_active' => 1,
        'content' => <<<EOT
<ul>
<li class="first"><a href="#"><img src="{{skin_url='images/block_off.png'}}" alt="block_off" /></a></li>
<li><a href="#"><img src="{{skin_url='images/block_chance.png'}}" alt="steve_logo" /></a></li>
<li class="last"><a href="#"><img src="{{skin_url='images/block_pumps.png'}}" alt="block_pumps" /></a></li>
</ul>
EOT
    ,
    ),

	array(
		'identifier' => 'stevemadden',
		'title' => 'SteveMadden',
		'is_active' => 1,
		'content' => <<<EOT
<div class="slider stevemadden_slider clearer">{{block type="interaktingslider/interaktingslider" name="interaktingslider" group="stevemadden"}}</div>
<div class="more-views">
<div class="more-left" style="display: block;"><span>left</span></div>
<div class="more-right" style="display: block;"><span>right</span></div>
</div>
<div class="block_list">{{block type="cms/block" name="cms_test_block" block_id="stevemadden_block" }}</div>
EOT
		,
	),

	array(
		'identifier' => 'stevemadden_banner',
		'title' => 'Steve Madden Banner',
		'is_active' => 1,
		'content' => <<<EOT
<div class="brand-banner">
<div class="brand-image"><img src="{{skin_url='images/steve_logo.png'}}" alt="steve_logo" /></div>
<div id="sub_menu">
<div class="container">
<ul>
<li><a class="shop" href="{{store_url='stevemadden/shop.html'}}">SHOP STEVE MADDEN</a>
<div class="child_menu">
<ul>
<li><a href="{{store_url='stevemadden/shop/new-arrivals.html'}}">NEW ARRIVALS</a></li>
<li><a href="{{store_url='stevemadden/shop/best-sellers.html'}}">BEST SELLERS</a></li>
<li><a href="{{store_url='eqiq/shop/editor.html'}}">EDITOR'S PICKS</a></li>
<li><a href="{{store_url='stevemadden/shop/exclusives.html'}}">EXCLUSIVES</a></li>
</ul>
<ul>
<li><a href="{{store_url='stevemadden/shop/shoes.html'}}">SHOES</a></li>
<li><a href="{{store_url='stevemadden/shop/accessories.html'}}">ACCESSROIES</a></li>
</ul>
</div>
</li>
<li><a href="{{store_url='stevemadden/about'}}">ABOUT</a></li>
<li><a href="{{store_url='stevemadden/lookbook'}}">LOOK BOOK</a></li>
<li><a href="{{store_url='stevemadden/store'}}">STORE LOCATOR</a></li>
</ul>
<div class="clear">&nbsp;</div>
</div>
</div>
<script type="text/javascript">// <![CDATA[
		jQuery(function(){
		jQuery('.shop').parent('li').hover(
		function(){jQuery('.child_menu').show(); jQuery(this).addClass('selected');},
		function(){jQuery('.child_menu').hide(); jQuery(this).removeClass('selected');}
	);
		jQuery('.child_menu').hover(
		function(){jQuery('.child_menu').show(); jQuery('.shop').addClass('selected');},
		function(){jQuery('.child_menu').hide(); jQuery('.shop').removeClass('selected');}
	);
	});
// ]]></script>
<div>&nbsp;</div>
</div>
EOT
		,
	),

	array(
		'identifier' => 'betseyjohnson_banner',
		'title' => 'Betsey Johnson Banner',
		'is_active' => 1,
		'content' => <<<EOT
<div class="brand-banner">
<div class="brand-image"><img src="{{skin_url='images/bj_logo.png'}}" alt="bj_logo" /></div>
<div id="sub_menu">
<div class="container">
<ul>
<li><a class="shop" href="{{store_url='betseyjohnson/shop.html'}}">SHOP BETSEY JOHNSON</a>
<div class="child_menu">
<ul>
<li><a href="{{store_url='betseyjohnson/shop/new-arrivals.html'}}">NEW ARRIVALS</a></li>
<li><a href="{{store_url='betseyjohnson/shop/best-sellers.html'}}">BEST SELLERS</a></li>
<li><a href="{{store_url='eqiq/shop/editor.html'}}">EDITOR'S PICKS</a></li>
<li><a href="{{store_url='betseyjohnson/shop/exclusives.html'}}">EXCLUSIVES</a></li>
</ul>
<ul>
<li><a href="{{store_url='betseyjohnson/shop/shoes.html'}}">SHOES</a></li>
<li><a href="{{store_url='betseyjohnson/shop/accessories.html'}}">ACCESSROIES</a></li>
<li><a href="{{store_url='betseyjohnson/shop/clothing.html'}}">CLOTHING</a></li>
</ul>
</div>
</li>
<li><a href="{{store_url='betseyjohnson/about'}}">ABOUT</a></li>
<li><a href="{{store_url='betseyjohnson/lookbook'}}">LOOK BOOK</a></li>
<li><a href="{{store_url='betseyjohnson/store'}}">STORE LOCATOR</a></li>
</ul>
<div class="clear">&nbsp;</div>
</div>
</div>
<script type="text/javascript">// <![CDATA[
		jQuery(function(){
		jQuery('.shop').parent('li').hover(
		function(){jQuery('.child_menu').show(); jQuery(this).addClass('selected');},
		function(){jQuery('.child_menu').hide(); jQuery(this).removeClass('selected');}
	);
		jQuery('.child_menu').hover(
		function(){jQuery('.child_menu').show(); jQuery('.shop').addClass('selected');},
		function(){jQuery('.child_menu').hide(); jQuery('.shop').removeClass('selected');}
	);
	});
// ]]></script>
<div>&nbsp;</div>
</div>
EOT
		,
	),
    array(
		'identifier' => 'ninewest',
		'title' => 'Nine West',
		'is_active' => 1,
		'content' => <<<EOT
<div class="slidershow ninewest-slide"><a href="{{store_url='ninewest/shop/new-arrivals.html'}}"><img src="{{skin_url='images/ninewest-new.png'}}" alt="banner" width="920" height="540" /></a></div>
EOT
		,
	),
    array(
		'identifier' => 'eqiq',
		'title' => 'EQIQ',
		'is_active' => 1,
		'content' => <<<EOT
<div class="slider bot_space eqiq-slide-img clearer">{{block type="interaktingslider/interaktingslider" name="interaktingslider" group="eqiq"}}</div>
<p>{{block type="cms/block" name="cms_test_block" block_id="eqiq_lookbook_viewall" }} {{block type="cms/block" name="cms_test_block" block_id="eqiq_bestsellers " }}</p>
EOT
		,
	),
    array(
		'identifier' => 'eqiq_banner',
		'title' => 'EQ:IQ Banner',
		'is_active' => 1,
		'content' => <<<EOT
<div class="brand-banner">
<div class="brand-image"><img src="{{skin_url='images/eqiq-banner.png'}}" alt="banner" /></div>
<div id="sub_menu">
<div class="container">
<ul>
<li><a class="shop" href="{{store_url='eqiq/shop.html'}}">SHOP EQ:IQ</a>
<div class="child_menu">
<ul>
<li><a href="{{store_url='eqiq/shop/new-arrivals.html'}}">NEW ARRIVALS</a></li>
<li><a href="{{store_url='eqiq/shop/best-sellers.html'}}">BEST SELLERS</a></li>
<li><a href="{{store_url='eqiq/shop/editor.html'}}">EDITOR'S PICKS</a></li>
<li><a href="{{store_url='eqiq/shop/exclusives.html'}}">EXCLUSIVES</a></li>
</ul>
<ul>
<li><a href="{{store_url='eqiq/shop/shoes.html'}}">SHOES</a></li>
<li><a href="{{store_url='eqiq/shop/accessroies.html'}}">ACCESSROIES</a></li>
<li><a href="{{store_url='eqiq/shop/clothing.html'}}">CLOTHING</a></li>
</ul>
</div>
</li>
<li><a href="{{store_url='eqiq/about'}}">ABOUT</a></li>
<li><a href="{{store_url='eqiq/lookbook'}}">LOOK BOOK</a></li>
<li><a href="{{store_url='eqiq/store'}}">STORE LOCATOR</a></li>
</ul>
<div class="clear">&nbsp;</div>
</div>
</div>
<script type="text/javascript">// <![CDATA[
		jQuery(function(){
		jQuery('.shop').parent('li').hover(
		function(){jQuery('.child_menu').show(); jQuery(this).addClass('selected');},
		function(){jQuery('.child_menu').hide(); jQuery(this).removeClass('selected');}
	);
		jQuery('.child_menu').hover(
		function(){jQuery('.child_menu').show(); jQuery('.shop').addClass('selected');},
		function(){jQuery('.child_menu').hide(); jQuery('.shop').removeClass('selected');}
	);
	});
// ]]></script>
<div>&nbsp;</div>
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


