<?php
/* @var $installer Gri_Cms_Model_Mysql4_Setup */
$installer = $this;
$installer->startSetup();

/* @var $block Mage_Cms_Model_Block */
$block = Mage::getModel('cms/block');
$data = array(
    array(
        'identifier' => 'ninewest_banner',
        'title' => 'Nine West Banner',
        'is_active' => 1,
        'content' => '
    	<div class="brand-banner">
<div class="brand-image"><img src="{{skin_url=\'images/ninewest-banner.jpg\'}}" alt="banner" /></div>
<div id="sub_menu">
<div class="container">
<ul>
<li><a class="shop" href="{{store_url=\'ninewest/shop.html\'}}">SHOP NINE WEST</a>
<div class="child_menu">
<ul>
<li><a href="{{store_url=\'ninewest/shop/new-arrivals.html\'}}">NEW ARRIVALS</a></li>
<li><a href="{{store_url=\'ninewest/shop/best-sellers.html\'}}">BEST SELLERS</a></li>
<li><a href="{{store_url=\'ninewest/shop/sale.html\'}}">SALE</a></li>
<li><a href="{{store_url=\'ninewest/shop/exclusives.html\'}}">EXCLUSIVES</a></li>
<li><a href="{{store_url=\'ninewest/shop/shop-by-look.html\'}}">SHOP BY LOOK</a></li>
</ul>
<ul>
<li><a href="{{store_url=\'ninewest/shop/shoes.html\'}}">SHOES</a></li>
<li><a href="{{store_url=\'ninewest/shop/accessroies.html\'}}">ACCESSROIES</a></li>
<li><a href="{{store_url=\'ninewest/shop/clothing.html\'}}">CLOTHING</a></li>
</ul>
</div>
</li>
<li><a href="{{store_url=\'ninewest/about\'}}">ABOUT</a></li>
<li><a href="{{store_url=\'ninewest/lookbook\'}}">LOOK BOOK</a></li>
<li><a href="{{store_url=\'ninewest/store\'}}">STORE LOCATOR</a></li>
</ul>
<div class="clear">&nbsp;</div>
</div>
</div>
<script type="text/javascript">// <![CDATA[
		jQuery(function(){
			jQuery(\'.shop\').parent(\'li\').hover(
				function(){jQuery(\'.child_menu\').show(); jQuery(this).addClass(\'selected\');},
				function(){jQuery(\'.child_menu\').hide(); jQuery(this).removeClass(\'selected\');}
			);
			jQuery(\'.child_menu\').hover(
				function(){jQuery(\'.child_menu\').show(); jQuery(\'.shop\').addClass(\'selected\');},
				function(){jQuery(\'.child_menu\').hide(); jQuery(\'.shop\').removeClass(\'selected\');}
			);
		});
// ]]></script>
<div>&nbsp;</div>
</div>
    	',
    ),
	array(
		'identifier' => 'eqiq_banner',
		'title' => 'EQ:IQ Banner',
		'is_active' => 1,
		'content' => '
		<div class="brand-banner">
		<div class="brand-image"><img src="{{skin_url=\'images/ninewest-banner.jpg\'}}" alt="banner" /></div>
		<div id="sub_menu">
		<div class="container">
		<ul>
		<li><a class="shop" href="{{store_url=\'eqiq/shop.html\'}}">SHOP EQ:IQ</a>
		<div class="child_menu">
		<ul>
		<li><a href="{{store_url=\'eqiq/shop/new-arrivals.html\'}}">NEW ARRIVALS</a></li>
		<li><a href="{{store_url=\'eqiq/shop/best-sellers.html\'}}">BEST SELLERS</a></li>
		<li><a href="{{store_url=\'eqiq/shop/sale.html\'}}">SALE</a></li>
		<li><a href="{{store_url=\'eqiq/shop/exclusives.html\'}}">EXCLUSIVES</a></li>
		</ul>
		<ul>
		<li><a href="{{store_url=\'eqiq/shop/shoes.html\'}}">SHOES</a></li>
		<li><a href="{{store_url=\'eqiq/shop/accessroies.html\'}}">ACCESSROIES</a></li>
		<li><a href="{{store_url=\'eqiq/shop/clothing.html\'}}">CLOTHING</a></li>
		</ul>
		</div>
		</li>
		<li><a href="{{store_url=\'eqiq/about\'}}">ABOUT</a></li>
		<li><a href="{{store_url=\'eqiq/lookbook\'}}">LOOK BOOK</a></li>
		<li><a href="{{store_url=\'eqiq/store\'}}">STORE LOCATOR</a></li>
		</ul>
		<div class="clear">&nbsp;</div>
		</div>
		</div>
		<script type="text/javascript">// <![CDATA[
		jQuery(function(){
		jQuery(\'.shop\').parent(\'li\').hover(
		function(){jQuery(\'.child_menu\').show(); jQuery(this).addClass(\'selected\');},
		function(){jQuery(\'.child_menu\').hide(); jQuery(this).removeClass(\'selected\');}
	);
		jQuery(\'.child_menu\').hover(
		function(){jQuery(\'.child_menu\').show(); jQuery(\'.shop\').addClass(\'selected\');},
		function(){jQuery(\'.child_menu\').hide(); jQuery(\'.shop\').removeClass(\'selected\');}
	);
	});
		// ]]></script>
		<div>&nbsp;</div>
		</div>
		',
	),

	array(
		'identifier' => 'stevemadden_banner',
		'title' => 'Steve Madden Banner',
		'is_active' => 1,
		'content' => '
		<div class="brand-banner">
		<div class="brand-image"><img src="{{skin_url=\'images/ninewest-banner.jpg\'}}" alt="banner" /></div>
		<div id="sub_menu">
		<div class="container">
		<ul>
		<li><a class="shop" href="{{store_url=\'stevemadden/shop.html\'}}">SHOP STEVE MADDEN</a>
		<div class="child_menu">
		<ul>
		<li><a href="{{store_url=\'stevemadden/shop/new-arrivals.html\'}}">NEW ARRIVALS</a></li>
		<li><a href="{{store_url=\'stevemadden/shop/best-sellers.html\'}}">BEST SELLERS</a></li>
		<li><a href="{{store_url=\'stevemadden/shop/sale.html\'}}">SALE</a></li>
		<li><a href="{{store_url=\'stevemadden/shop/exclusives.html\'}}">EXCLUSIVES</a></li>
		</ul>
		<ul>
		<li><a href="{{store_url=\'stevemadden/shop/shoes.html\'}}">SHOES</a></li>
		<li><a href="{{store_url=\'stevemadden/shop/accessroies.html\'}}">ACCESSROIES</a></li>
		<li><a href="{{store_url=\'stevemadden/shop/clothing.html\'}}">CLOTHING</a></li>
		</ul>
		</div>
		</li>
		<li><a href="{{store_url=\'stevemadden/about\'}}">ABOUT</a></li>
		<li><a href="{{store_url=\'stevemadden/lookbook\'}}">LOOK BOOK</a></li>
		<li><a href="{{store_url=\'stevemadden/store\'}}">STORE LOCATOR</a></li>
		</ul>
		<div class="clear">&nbsp;</div>
		</div>
		</div>
		<script type="text/javascript">// <![CDATA[
		jQuery(function(){
		jQuery(\'.shop\').parent(\'li\').hover(
		function(){jQuery(\'.child_menu\').show(); jQuery(this).addClass(\'selected\');},
		function(){jQuery(\'.child_menu\').hide(); jQuery(this).removeClass(\'selected\');}
	);
		jQuery(\'.child_menu\').hover(
		function(){jQuery(\'.child_menu\').show(); jQuery(\'.shop\').addClass(\'selected\');},
		function(){jQuery(\'.child_menu\').hide(); jQuery(\'.shop\').removeClass(\'selected\');}
	);
	});
		// ]]></script>
		<div>&nbsp;</div>
		</div>
		',
	),

	array(
		'identifier' => 'betseyjohnson_banner',
		'title' => 'Betsey Johnson Banner',
		'is_active' => 1,
		'content' => '
		<div class="brand-banner">
		<div class="brand-image"><img src="{{skin_url=\'images/ninewest-banner.jpg\'}}" alt="banner" /></div>
		<div id="sub_menu">
		<div class="container">
		<ul>
		<li><a class="shop" href="{{store_url=\'betseyjohnson/shop.html\'}}">SHOP BETSEY JOHNSON</a>
		<div class="child_menu">
		<ul>
		<li><a href="{{store_url=\'betseyjohnson/shop/new-arrivals.html\'}}">NEW ARRIVALS</a></li>
		<li><a href="{{store_url=\'betseyjohnson/shop/best-sellers.html\'}}">BEST SELLERS</a></li>
		<li><a href="{{store_url=\'betseyjohnson/shop/sale.html\'}}">SALE</a></li>
		<li><a href="{{store_url=\'betseyjohnson/shop/exclusives.html\'}}">EXCLUSIVES</a></li>
		</ul>
		<ul>
		<li><a href="{{store_url=\'betseyjohnson/shop/shoes.html\'}}">SHOES</a></li>
		<li><a href="{{store_url=\'betseyjohnson/shop/accessroies.html\'}}">ACCESSROIES</a></li>
		<li><a href="{{store_url=\'betseyjohnson/shop/clothing.html\'}}">CLOTHING</a></li>
		</ul>
		</div>
		</li>
		<li><a href="{{store_url=\'betseyjohnson/about\'}}">ABOUT</a></li>
		<li><a href="{{store_url=\'betseyjohnson/lookbook\'}}">LOOK BOOK</a></li>
		<li><a href="{{store_url=\'betseyjohnson/store\'}}">STORE LOCATOR</a></li>
		</ul>
		<div class="clear">&nbsp;</div>
		</div>
		</div>
		<script type="text/javascript">// <![CDATA[
		jQuery(function(){
		jQuery(\'.shop\').parent(\'li\').hover(
		function(){jQuery(\'.child_menu\').show(); jQuery(this).addClass(\'selected\');},
		function(){jQuery(\'.child_menu\').hide(); jQuery(this).removeClass(\'selected\');}
	);
		jQuery(\'.child_menu\').hover(
		function(){jQuery(\'.child_menu\').show(); jQuery(\'.shop\').addClass(\'selected\');},
		function(){jQuery(\'.child_menu\').hide(); jQuery(\'.shop\').removeClass(\'selected\');}
	);
	});
		// ]]></script>
		<div>&nbsp;</div>
		</div>
		',
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

