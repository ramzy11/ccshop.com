<?php
/* @var $this Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
$installer = $this;
$installer->startSetup();


/* @var $block Mage_Cms_Model_Block */
$block = Mage::getModel('cms/block');
$data = array(
    array(
        'identifier' => 'carolinnaespinosa',
        'title' => 'Carolinna Espinosa',
        'content' => <<<EOT
<div class="slider bot_space carolinnaespinosa-slide-img clearer">{{block type="interaktingslider/interaktingslider" name="interaktingslider" group="carolinnaespinosa"}}</div>
EOT
    ,
    ),
    array(
        'identifier' => 'carolinnaespinosa_banner',
        'title' => 'Carolinna Espinosa Banner',
        'content' => <<<EOT
<div class="brand-banner">
<div class="brand-image"><img title="Carolinna Espinosa" src="{{media url="wysiwyg/Jan_28_Soft_Launch/NineWest/B2.jpg"}}" alt="Carolinna Espinosa" /></div>
<div id="sub_menu">
<div class="container">
<ul>
<li class="first"><a class="shop" href="{{store_url='carolinnaespinosa/shop.html'}}">Shop</a>
<div class="child_menu">
<ul>
<li><a href="{{store_url='carolinnaespinosa/shop/new-arrivals.html'}}">New Arrivals</a></li>
<li><a href="{{store_url='carolinnaespinosa/shop/editor.html'}}">Editor's Pick</a></li>
<li><a href="{{store_url='carolinnaespinosa/shop/best-sellers.html'}}">Best Sellers</a></li>
</ul>
<ul>
<li><a href="{{store_url='carolinnaespinosa/shop/shoes.html'}}">Shoes</a></li>
<li><a href="{{store_url='carolinnaespinosa/shop/clothing.html'}}">Clothing</a></li>
<li><a href="{{store_url='carolinnaespinosa/shop/accessories.html'}}">Accessories</a></li>
</ul>
</div>
</li>
<li><a href="{{store_url='carolinnaespinosa/about'}}">About Carolinna Espinosa</a></li>
<li><a href="{{store_url='carolinnaespinosa/store'}}">Store Locator</a></li>
<li>&nbsp;</li>
</ul>
<div class="clear">&nbsp;</div>
</div>
</div>
<div>&nbsp;</div>
</div>
EOT
    ,
    ),
    array(
        'identifier' => 'carolinnaespinosa_nav',
        'title' => 'Carolinna Espinosa Nav',
        'content' => <<<EOT
<div class="left-static-nav">
<ul>
<li class="new-arrivals"><a href="{{store_url='carolinnaespinosa/shop/new-arrivals.html'}}">New Arrivals</a></li>
<li class="editors_pick"><a href="{{store_url='carolinnaespinosa/shop/editor.html'}}">Editor's Pick</a></li>
<li class="editors_pick"><a href="{{store_url='carolinnaespinosa/shop/best-sellers.html'}}">Best Sellers</a></li>
</ul>
</div>
EOT
    ,
    ),
    array(
        'identifier' => 'carolinnaespinosa-shop',
        'title' => 'Carolinna Espinosa Shop',
        'content' => <<<EOT
<div class="banner">
<p class="spacer">{{block type="gri_catalogcustom/category_group" name="carolinnaespinosa-shop"}}</p>
</div>
EOT
    ,
    ),
    array(
        'identifier' => 'carolinnaespinosa-shop-shoes',
        'title' => 'Carolinna Espinosa Shop Shoes',
        'content' => <<<EOT
<div class="banner">
<p class="spacer">{{block type="gri_catalogcustom/category_group" name="carolinnaespinosa-shop-shoes"}}</p>
</div>
EOT
    ,
    ),
    array(
        'identifier' => 'carolinnaespinosa-shop-clothing',
        'title' => 'Carolinna Espinosa Shop Clothing',
        'content' => <<<EOT
<div class="banner">
<p class="spacer">{{block type="gri_catalogcustom/category_group" name="carolinnaespinosa-shop-clothing"}}</p>
</div>
EOT
    ,
    ),
    array(
        'identifier' => 'carolinnaespinosa-shop-accessories',
        'title' => 'Carolinna Espinosa Shop Accessories',
        'content' => <<<EOT
<div class="banner">
<p class="spacer">{{block type="gri_catalogcustom/category_group" name="carolinnaespinosa-shop-accessories"}}</p>
</div>
EOT
    ,
    ),
);
$blocks = array();
foreach ($data as $d) {
    $block->unsetData();
    $block->setStoreId(1)->setLoadInactive(TRUE)->load($d['identifier']);
    $block->addData($d);
    $block->setStores(array(1))->save();
    $blocks[1][$block->getIdentifier()] = $block->getId();
    $block->unsetData();
    $block->setStoreId(2)->setLoadInactive(TRUE)->load($d['identifier']);
    $block->addData($d);
    $block->setStores(array(2))->save();
    $blocks[2][$block->getIdentifier()] = $block->getId();
}

/* @var $helper Gri_CatalogCustom_Helper_Category */
$helper = Mage::helper('gri_catalogcustom/category');

$dmPage = Mage_Catalog_Model_Category::DM_PAGE;
$dmProducts = Mage_Catalog_Model_Category::DM_PRODUCT;
$definition = array(
    'carolinnaespinosa' => array(
        'name' => 'Carolinna Espinosa',
        'include_in_menu' => 0,
        'display_mode' => $dmPage,
        'landing_page' => 'carolinnaespinosa',
        'children' => array(
            'shop' => array(
                'name' => 'Shop',
                'display_mode' => $dmPage,
                'landing_page' => 'carolinnaespinosa-shop',
                'children' => array(
                    'best-sellers' => array(
                        'name' => 'Best Sellers',
                        'include_in_menu' => 0,
                        'display_mode' => $dmPage,
                        'landing_page' => 'best_seller',
                    ),
                    'new-arrivals' => array(
                        'name' => 'New Arrivals',
                        'include_in_menu' => 0,
                        'display_mode' => $dmPage,
                        'landing_page' => 'new-arrivals',
                    ),
                    'exclusives' => array(
                        'name' => 'Exclusives',
                    ),
                    'editor' => array(
                        'name' => 'Editor\'s Picks',
                        'include_in_menu' => 0,
                        'display_mode' => $dmPage,
                        'landing_page' => 'editors_pick',
                    ),
                    'pre-order' => array(
                        'name' => 'Pre Order',
                        'include_in_menu' => 0,
                        'display_mode' => $dmPage,
                        'landing_page' => 'pre-order',
                    ),
                    'pre-sales' => array(
                        'name' => 'Pre Sales',
                        'include_in_menu' => 0,
                        'display_mode' => $dmPage,
                        'landing_page' => 'pre-sales',
                    ),
                    'shoes' => array(
                        'name' => 'Shoes',
                        'display_mode' => $dmPage,
                        'landing_page' => 'carolinnaespinosa-shop-shoes',
                        'children' => array(
                            'pumps' => array(
                                'name' => 'Pumps',
                                'children' => array(
                                    'high-heel' => array(
                                        'name' => 'High Heel',
                                    ),
                                    'mid-heel' => array(
                                        'name' => 'Mid Heel',
                                    ),
                                    'platforms' => array(
                                        'name' => 'Platforms',
                                    ),
                                    'wedges' => array(
                                        'name' => 'Wedges',
                                    ),
                                ),
                            ),
                            'flats' => array(
                                'name' => 'Flats',
                            ),
                            'sandals' => array(
                                'name' => 'Sandals',
                                'children' => array(
                                    'flat' => array(
                                        'name' => 'Flat',
                                    ),
                                    'high-heel' => array(
                                        'name' => 'High Heel',
                                    ),
                                    'wedges' => array(
                                        'name' => 'Wedges',
                                    ),
                                    'platforms' => array(
                                        'name' => 'Platforms',
                                    ),
                                ),
                            ),
                            'boots' => array(
                                'name' => 'Boots',
                            ),
                            'booties' => array(
                                'name' => 'Booties',
                            ),
                            'platforms' => array(
                                'name' => 'Platforms',
                            ),
                            'wedges' => array(
                                'name' => 'Wedges',
                            ),
                        ),
                    ),
                    'clothing' => array(
                        'name' => 'Clothing',
                        'display_mode' => $dmPage,
                        'landing_page' => 'carolinnaespinosa-shop-clothing',
                        'children' => array(
                            'tops' => array(
                                'name' => 'Tops',
                            ),
                            'dresses' => array(
                                'name' => 'Dresses',
                            ),
                            'skirts' => array(
                                'name' => 'Skirts',
                            ),
                            'pants-and-shorts' => array(
                                'name' => 'Pants and Shorts',
                            ),
                            'outerwear' => array(
                                'name' => 'Outerwear',
                            ),
                            'knitwear' => array(
                                'name' => 'Knitwear',
                            ),
                        ),
                    ),
                    'accessories' => array(
                        'name' => 'Accessories',
                        'display_mode' => $dmPage,
                        'landing_page' => 'carolinnaespinosa-shop-accessories',
                        'children' => array(
                            'bags' => array(
                                'name' => 'Bags',
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
);
$root = Mage::getModel('catalog/category')->setStoreId(0)->load(2);
$helper->createCategories($definition, $root, $blocks);

$installer->endSetup();
