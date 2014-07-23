<?php
/* @var $installer Gri_Cms_Model_Mysql4_Setup */
$installer = $this;
$installer->startSetup();

/* @var $block Mage_Cms_Model_Block */
$block = Mage::getModel('cms/block');
$data = array(
    array(
        'identifier' => 'footer_links',
        'title' => 'Footer Links',
        'is_active' => 1,
        'content' => <<<EOT
<div class="footer_col">
    <span class="fotTitle">BRANDS</span>
    <ul>
        <li><a href="{{store_url='ninewest.html'}}">Nine West</a></li>
        <li><a href="{{store_url='stevemadden.html'}}">Steve Madden</a></li>
        <li><a href="{{store_url='betseyjohnson.html'}}">Betsey Johnson</a></li>
        <li><a href="{{store_url='eqiq.html'}}">EQ:IQ</a></li>
    </ul>
    <p>&nbsp;</p>
    <span class="fotTitle">MEMBERSHIP</span>
    <ul>
        <li><a href="#">VIP</a></li>
    </ul>
</div>
<div class="footer_col">
    <span class="fotTitle">CUSTOMER SERVICE</span>
    <ul>
        <li><a href="#">How to shop</a></li>
        <li><a href="#">Payment</a></li>
        <li><a href="{{store_url='customer-service/shipping-delivery'}}">Shipping & Delivery</a></li>
        <li><a href="{{store_url='customer-service/returns'}}">Returns</a></li>
        <li><a href="{{store_url='customer-service/faq'}}">FAQ</a></li>
    </ul>
</div>
<div class="footer_col">
    <span class="fotTitle">SHOPPING TIPS</span>
    <ul>
        <li><a href="#">Size Chart</a></li>
        <li><a href="{{store_url='customer-service/care-cleaning-tips'}}">Care & Cleaning Tips</a></li>
        <li><a href="{{store_url='customer-service/shoes-glossary'}}">Shoe Glossary</a></li>
        <li><a href="{{store_url='customer-service/clothing-glossary'}}">Clothing Glossary</a></li>
    </ul>
</div>
<div class="footer_col">
    <span class="fotTitle">ABOUT US</span>
    <ul>
        <li><a href="{{store_url='about-us/central-central'}}">About Central/Central</a></li>
        <li><a href="{{store_url='about-us/store-locator'}}">Store locator</a></li>
        <li><a href="{{store_url='about-us/contact'}}">Contact Us</a></li>
        <li><a href="{{store_url='sitemap'}}">Sitemap</a></li>
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

