<?php
/* Menu Blocks */
/* @var $installer Gri_Cms_Model_Mysql4_Setup */
$installer = $this;
$installer->startSetup();


/* @var $block Mage_Cms_Model_Block */
$block = Mage::getModel('cms/block');
$blockResourceModel = Mage::getResourceModel('cms/block');

$data = array(
    //Chinese
    1 => array(
        //Menu Title
        array(
            'identifier' => 'clothing_menu_title',
            'title' => ' 服裝菜單標題[HK]',
            'is_active' => 1,
            'content' => '<p><span>類型</span></p>'
        ,
        ),
        array(
            'identifier' => 'shoes_menu_title',
            'title' => '鞋履菜單標題[HK]',
            'is_active' => 1,
            'content' => '<p><span>類型</span></p>'
        ,
        ),
        array(
            'identifier' => 'bags_menu_title',
            'title' => '包袋菜單標題[HK]',
            'is_active' => 1,
            'content' => '<p><span>類型</span></p>'
        ,
        ),
        array(
            'identifier' => 'accessories_menu_title',
            'title' => '配飾菜單標題[HK]',
            'is_active' => 1,
            'content' => '<p><span>類型</span></p>'
        ,
        ),
        array(
            'identifier' => 'all-brands_menu_title',
            'title' => '品牌菜單標題[HK]',
            'is_active' => 1,
            'content' => '<p><span>在線品牌</span></p>'
        ,
        ),
        array(
            'identifier' => 'all-new-arrivals_menu_title',
            'title' => '新品菜單標題[HK]',
            'is_active' => 1,
            'content' => '<p><span>類型</span></p>'
        ,
        ),

        //Menu Content
        array(
            'identifier' => 'shoes_menu_content',
            'title' => '鞋履菜單內容[HK]',
            'is_active' => 1,
            'content' => <<<EOT
<div class="shop-by-brand">
<p><span>品牌</span></p>
<ul>
<li><a href="{{store_url='ninewest/shop/shoes.html'}}"><span>Nine West</span></a></li>
<li><a href="{{store_url='eqiq/shop/shoes.html'}}"><span>EQ:IQ</span></a></li>
<li><a href="{{store_url='stevemadden/shop/shoes.html'}}"><span>Steve Madden</span></a></li>
<li><a href="{{store_url='carolinnaespinosa/shop/shoes.html'}}"><span>Carolinna Espinosa</span></a></li>
<li><a href="{{store_url='betseyjohnson/shop/shoes.html'}}"><span>Betsey Johnson</span></a></li>
</ul>
</div>
<div class="menu-image"><img src="{{media url="wysiwyg/menu-shoes.jpg"}}" alt="" /></div>
EOT
        ,
        ),

        array(
            'identifier' => 'clothing_menu_content',
            'title' => '服裝菜單內容[HK]',
            'is_active' => 1,
            'content' => <<<EOT
<div class="shop-by-brand">
<p><span>品牌</span></p>
<ul>
<li><a href="{{store_url='ninewest/shop/clothing.html'}}"><span>Nine West</span></a></li>
<li><a href="{{store_url='eqiq/shop/clothing.html'}}"><span>EQ:IQ</span></a></li>
<li><a href="{{store_url='stevemadden/shop/clothing.html'}}"><span>Steve Madden</span></a></li>
<li><a href="{{store_url='carolinnaespinosa/shop/clothing.html'}}"><span>Carolinna Espinosa</span></a></li>
<li><a href="{{store_url='betseyjohnson/shop/clothing.html'}}"><span>Betsey Johnson</span></a></li>
</ul>
</div>
<div class="menu-image"><img src="{{media url="wysiwyg/menu-clothing.jpg"}}" alt="" /></div>
EOT
        ,
        ),

        array(
            'identifier' => 'accessories_menu_content',
            'title' => '配飾菜單內容[HK]',
            'is_active' => 1,
            'content' => <<<EOT
<div class="shop-by-brand">
<p><span>品牌</span></p>
<ul>
<li><a href="{{store_url='ninewest/shop/accessories.html'}}"><span>Nine West</span></a></li>
<li><a href="{{store_url='eqiq/shop/accessories.html'}}"><span>EQ:IQ</span></a></li>
<li><a href="{{store_url='stevemadden/shop/accessories.html'}}"><span>Steve Madden</span></a></li>
<li><a href="{{store_url='carolinnaespinosa/shop/accessories.html'}}"><span>Carolinna Espinosa</span></a></li>
<li><a href="{{store_url='betseyjohnson/shop/accessories.html'}}"><span>Betsey Johnson</span></a></li>
</ul>
</div>
<div class="menu-image"><img src="{{media url="wysiwyg/menu-accessories.jpg"}}" alt="" /></div>
EOT
        ,
        ),

        array(
            'identifier' => 'bags_menu_content',
            'title' => '包袋菜單內容[HK]',
            'is_active' => 1,
            'content' => <<<EOT
<div class="shop-by-brand">
<p><span>品牌</span></p>
<ul>
<li><a href="{{store_url='ninewest/shop/bags.html'}}"><span>Nine West</span></a></li>
<li><a href="{{store_url='eqiq/shop/bags.html'}}"><span>EQ:IQ</span></a></li>
<li><a href="{{store_url='stevemadden/shop/bags.html'}}"><span>Steve Madden</span></a></li>
<li><a href="{{store_url='carolinnaespinosa/shop/bags.html'}}"><span>Carolinna Espinosa</span></a></li>
<li><a href="{{store_url='betseyjohnson/shop/bags.html'}}"><span>Betsey Johnson</span></a></li>
</ul>
</div>
<div class="menu-image"><img src="{{media url="wysiwyg/menu-bags.jpg"}}" alt="" /></div>
EOT
        ,
        ),

        array(
            'identifier' => 'all-brands_menu_content',
            'title' => '品牌菜單內容[HK]',
            'is_active' => 1,
            'content' => <<<EOT
<div class="shop-by-brand">
<p><span>即將推出</span></p>
<ul>
<li><a href="{{store_url='anne-klein.html'}}"><span>Anne Klein</span></a></li>
<li><a href="{{store_url='easyspirit.html'}}"><span>Easy Spirit</span></a></li>
<li><a href="{{store_url='joan-david.html'}}"><span>Joan & David</span></a></li>
<li><a href="{{store_url='karenmillen.html'}}"><span>Karen Millen</span></a></li>
</ul>
</div>
<div class="menu-image"><img src="{{media url="wysiwyg/menu-brand.jpg"}}" alt="" /></div>
EOT
        ,
        ),

        array(
            'identifier' => 'all-new-arrivals_menu_content',
            'title' => '新品菜單內容[HK]',
            'is_active' => 1,
            'content' => <<<EOT
<div class="shop-by-brand">
<p><span>品牌</span></p>
<ul>
<li><a href="{{store_url='ninewest/shop/new-arrivals.html'}}"><span>Nine West</span></a></li>
<li><a href="{{store_url='eqiq/shop/new-arrivals.html'}}"><span>EQ:IQ</span></a></li>
<li><a href="{{store_url='stevemadden/shop/new-arrivals.html'}}"><span>Steve Madden</span></a></li>
<li><a href="{{store_url='carolinnaespinosa/shop/new-arrivals.html'}}"><span>Carolinna Espinosa</span></a></li>
<li><a href="{{store_url='betseyjohnson/shop/new-arrivals.html'}}"><span>Betsey Johnson</span></a></li>
</ul>
</div>
<div class="menu-image"><img src="{{media url="wysiwyg/menu-newarrivals.jpg"}}" alt="" /></div>
EOT
        ,
        ),
    ),

    //English
    2 => array(
        //Menu Title
        array(
            'identifier' => 'clothing_menu_title',
            'title' => 'Clothing Menu Title [EN]',
            'is_active' => 1,
            'content' => '<p><span>Shop By Category</span></p>'
        ,
        ),
        array(
            'identifier' => 'shoes_menu_title',
            'title' => 'Shoes Menu Title [EN]',
            'is_active' => 1,
            'content' => '<p><span>Shop By Category</span></p>'
        ,
        ),
        array(
            'identifier' => 'bags_menu_title',
            'title' => 'Shoes Menu Title [EN]',
            'is_active' => 1,
            'content' => '<p><span>Shop By Category</span></p>'
        ,
        ),
        array(
            'identifier' => 'accessories_menu_title',
            'title' => 'Accessories Menu Title [EN]',
            'is_active' => 1,
            'content' => '<p><span>Shop By Category</span></p>'
        ,
        ),
        array(
            'identifier' => 'all-brands_menu_title',
            'title' => 'Brands Menu Title [EN]',
            'is_active' => 1,
            'content' => '<p><span>Online Brands</span></p>'
        ,
        ),
        array(
            'identifier' => 'all-new-arrivals_menu_title',
            'title' => 'New Arrivals Menu Title [EN]',
            'is_active' => 1,
            'content' => '<p><span>Shop By Category</span></p>'
        ,
        ),



    //Menu Content
    array(
        'identifier' => 'shoes_menu_content',
        'title' => 'Shoes Menu Content [EN]',
        'is_active' => 1,
        'content' => <<<EOT
<div class="shop-by-brand">
<p><span>Shop By Brand</span></p>
<ul>
<li><a href="{{store_url='ninewest/shop/shoes.html'}}"><span>Nine West</span></a></li>
<li><a href="{{store_url='eqiq/shop/shoes.html'}}"><span>EQ:IQ</span></a></li>
<li><a href="{{store_url='stevemadden/shop/shoes.html'}}"><span>Steve Madden</span></a></li>
<li><a href="{{store_url='carolinnaespinosa/shop/shoes.html'}}"><span>Carolinna Espinosa</span></a></li>
<li><a href="{{store_url='betseyjohnson/shop/shoes.html'}}"><span>Betsey Johnson</span></a></li>
</ul>
</div>
<div class="menu-image"><img src="{{media url="wysiwyg/menu-shoes.jpg"}}" alt="" /></div>
EOT
    ,
    ),

    array(
        'identifier' => 'clothing_menu_content',
        'title' => 'Clothing Menu Content [EN]',
        'is_active' => 1,
        'content' => <<<EOT
<div class="shop-by-brand">
<p><span>Shop By Brand</span></p>
<ul>
<li><a href="{{store_url='ninewest/shop/clothing.html'}}"><span>Nine West</span></a></li>
<li><a href="{{store_url='eqiq/shop/clothing.html'}}"><span>EQ:IQ</span></a></li>
<li><a href="{{store_url='stevemadden/shop/clothing.html'}}"><span>Steve Madden</span></a></li>
<li><a href="{{store_url='carolinnaespinosa/shop/clothing.html'}}"><span>Carolinna Espinosa</span></a></li>
<li><a href="{{store_url='betseyjohnson/shop/clothing.html'}}"><span>Betsey Johnson</span></a></li>
</ul>
</div>
<div class="menu-image"><img src="{{media url="wysiwyg/menu-clothing.jpg"}}" alt="" /></div>
EOT
    ,
    ),

    array(
        'identifier' => 'accessories_menu_content',
        'title' => 'Accessories Menu Content [EN]',
        'is_active' => 1,
        'content' => <<<EOT
<div class="shop-by-brand">
<p><span>Shop By Brand</span></p>
<ul>
<li><a href="{{store_url='ninewest/shop/accessories.html'}}"><span>Nine West</span></a></li>
<li><a href="{{store_url='eqiq/shop/accessories.html'}}"><span>EQ:IQ</span></a></li>
<li><a href="{{store_url='stevemadden/shop/accessories.html'}}"><span>Steve Madden</span></a></li>
<li><a href="{{store_url='carolinnaespinosa/shop/accessories.html'}}"><span>Carolinna Espinosa</span></a></li>
<li><a href="{{store_url='betseyjohnson/shop/accessories.html'}}"><span>Betsey Johnson</span></a></li>
</ul>
</div>
<div class="menu-image"><img src="{{media url="wysiwyg/menu-accessories.jpg"}}" alt="" /></div>
EOT
    ,
    ),

    array(
        'identifier' => 'bags_menu_content',
        'title' => 'Bags Menu Content [EN]',
        'is_active' => 1,
        'content' => <<<EOT
<div class="shop-by-brand">
<p><span>Shop By Brand</span></p>
<ul>
<li><a href="{{store_url='ninewest/shop/bags.html'}}"><span>Nine West</span></a></li>
<li><a href="{{store_url='eqiq/shop/bags.html'}}"><span>EQ:IQ</span></a></li>
<li><a href="{{store_url='stevemadden/shop/bags.html'}}"><span>Steve Madden</span></a></li>
<li><a href="{{store_url='carolinnaespinosa/shop/bags.html'}}"><span>Carolinna Espinosa</span></a></li>
<li><a href="{{store_url='betseyjohnson/shop/bags.html'}}"><span>Betsey Johnson</span></a></li>
</ul>
</div>
<div class="menu-image"><img src="{{media url="wysiwyg/menu-bags.jpg"}}" alt="" /></div>
EOT
    ,
    ),

    array(
        'identifier' => 'all-brands_menu_content',
        'title' => 'Brands Menu Content [EN]',
        'is_active' => 1,
        'content' => <<<EOT
<div class="shop-by-brand">
<p><span>Coming soon</span></p>
<ul>
<li><a href="{{store_url='anne-klein.html'}}"><span>Anne Klein</span></a></li>
<li><a href="{{store_url='easyspirit.html'}}"><span>Easy Spirit</span></a></li>
<li><a href="{{store_url='joan-david.html'}}"><span>Joan & David</span></a></li>
<li><a href="{{store_url='karenmillen.html'}}"><span>Karen Millen</span></a></li>
</ul>
</div>
<div class="menu-image"><img src="{{media url="wysiwyg/menu-brand.jpg"}}" alt="" /></div>
EOT
    ,
    ),

        array(
            'identifier' => 'all-new-arrivals_menu_content',
            'title' => 'New Arrivals Menu Content [EN]',
            'is_active' => 1,
            'content' => <<<EOT
<div class="shop-by-brand">
<p><span>Shop by Brand</span></p>
<ul>
<li><a href="{{store_url='ninewest/shop/new-arrivals.html'}}"><span>Nine West</span></a></li>
<li><a href="{{store_url='stevemadden/shop/new-arrivals.html'}}"><span>Steve Madden</span></a></li>
<li><a href="{{store_url='carolinnaespinosa/shop/new-arrivals.html'}}"><span>Carolinna Espinosa</span></a></li>
<li><a href="{{store_url='betseyjohnson/shop/new-arrivals.html'}}"><span>Betsey Johnson</span></a></li>
</ul>
</div>
<div class="menu-image"><img src="{{media url="wysiwyg/menu-newarrivals.jpg"}}" alt="" /></div>
EOT
        ,
        ),

)
);


foreach ($data as $storeId => $content) {
    foreach($content as $d){
        $block->unsetData();

        if($blockId = $blockResourceModel->checkIdentifier($d['identifier'],$storeId)){
            $block = $block->load($blockId);
        }

        foreach ($d as $k => $v) {
            $block->setData($k, $v);
        }

        $block->setStores(array($storeId))->save();//set store view
    }
}

$installer->endSetup();