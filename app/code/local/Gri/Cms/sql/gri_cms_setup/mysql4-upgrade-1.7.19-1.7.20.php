<?php
/* @var $installer Gri_Cms_Model_Mysql4_Setup */
$installer = $this;
$installer->startSetup();

$cmsBlockStoreTable = $installer->getTable('cms/block_store');
$cmsPageStoreTable = $installer->getTable('cms/page_store');

$sql = "UPDATE `{$cmsBlockStoreTable}` SET `store_id` = 1;
UPDATE `{$cmsPageStoreTable}` SET `store_id` = 1;";
$installer->run($sql);

/* @var $contentHelper Gri_Cms_Helper_Content */
$contentHelper = Mage::helper('gri_cms/content');

Mage::app()->reinitStores();
foreach (Mage::app()->getStores() as $storeId => $store) {
    if ($storeId <= 1) continue;
    $contentHelper->updateStoreBlocks($store, array(
        'about-betseyjohnson',
        'about-eqiq',
        'about-ninewest',
        'about-stevemadden',
        'accessories_nav',
        'as-seen-in',
        'as-seen-in-celebrity',
        'as-seen-in-editorials',
        'as-seen-in-media',
        'best_seller',
        'betsey-johnson-look-book',
        'betseyjohnson',
        'betseyjohnson-concept',
        'betseyjohnson-meet-designer',
        'betseyjohnson-runway',
        'betseyjohnson-shop',
        'betseyjohnson-shop-accessories',
        'betseyjohnson-shop-clothing',
        'betseyjohnson-shop-shoes',
        'betseyjohnson-store',
        'betseyjohnson_banner',
        'betseyjohnson_nav',
        'cart-banner',
        'checkout-package',
        'clothing_nav',
        'editors_pick',
        'eqiq',
        'eqiq-shop',
        'eqiq-shop-accessories',
        'eqiq-shop-clothing',
        'eqiq-shop-shoes',
        'eqiq-store',
        'eqiq_banner',
        'eqiq_bestsellers',
        'eqiq_concept',
        'eqiq_lookbook',
        'eqiq_lookbook_viewall',
        'eqiq_nav',
        'events',
        'events-archives',
        'footer_links',
        'home_bottom_banner_1',
        'home_bottom_banner_2',
        'home_bottom_banner_3',
        'home_bottom_left',
        'home_center',
        'home_middle_right',
        'home_shop_now',
        'home_top_left',
        'home_top_right',
        'need_help',
        'new-arrivals',
        'ninewest',
        'ninewest-concept',
        'ninewest-inside-the-studio',
        'ninewest-meet-designer',
        'ninewest-shop',
        'ninewest-shop-accessories',
        'ninewest-shop-clothing',
        'ninewest-shop-shoes',
        'ninewest-store',
        'ninewest_banner',
        'ninewest_lookbook',
        'ninewest_nav',
        'product_optional_promotion_block',
        'product_promotion_rule_block',
        'safe_payment',
        'shoes_nav',
        'shop-accessories',
        'shop-clothing',
        'shop-shoes',
        'steve_madden_lookbook',
        'stevemadden',
        'stevemadden-concept',
        'stevemadden-meet-designer',
        'stevemadden-music',
        'stevemadden-shop',
        'stevemadden-shop-accessories',
        'stevemadden-shop-shoes',
        'stevemadden-store',
        'stevemadden_banner',
        'stevemadden_block',
        'stevemadden_nav',
        'vip-grading',
    ));
    $contentHelper->updateStorePages($store, array(
        'archives',
        'as-seen-in',
        'betseyjohnson/about',
        'betseyjohnson/lookbook',
        'betseyjohnson/store',
        'care-cleaning-tips',
        'careers',
        'celebrity',
        'central-central',
        'clothing-glossary',
        'contact',
        'customer-service',
        'editorials',
        'enable-cookies',
        'eqiq/about',
        'eqiq/lookbook',
        'eqiq/store',
        'events',
        'faq',
        'fitting-chart',
        'home',
        'media',
        'ninewest/about',
        'ninewest/lookbook',
        'ninewest/store',
        'no-route',
        'privacy-policy',
        'returns',
        'reward-points',
        'shipping-delivery',
        'shoe-size-chart',
        'shoes-glossary',
        'sitemap',
        'stevemadden/about',
        'stevemadden/lookbook',
        'stevemadden/store',
        'store-locator',
        'terms-of-use',
    ));
}

$installer->endSetup();
