<?php
/* @var $installer Gri_Cms_Model_Mysql4_Setup */
$installer = $this;
$installer->startSetup();


/* @var $block Mage_Cms_Model_Block */
$block = Mage::getModel('cms/block');
$data = array(
    array(
        'identifier' => 'checkout_cart_info',
        'title' => 'Checkout Cart Info',
        'is_active' => 1,
        'content' => <<<EOT
<div class="checkout-cart-info">
<ul>
<li class="first">
<h3>Free Shipping</h3>
<p>On over HK$1,000 or US$130</p>
</li>
<li>
<h3>7 days RETURNS</h3>
<p>You may return items within 7 days upon receipt</p>
</li>
<li>
<h3>Delivery</h3>
<p>2 - 7 working days under normal circumstances</p>
</li>
<li>
<h3>SECURE PAYMENT</h3>
<p>We accepts Paypal, Visa, MasterCard, American Express, JCB and UnionPay.</p>
</li>
<li class="last">
<h3>Customer Service</h3>
<p>Tel: +852-2480-2888</p>
<p>Available on Monday - Friday, 9 AM - 6 PM, excluding Hong Kong public holidays</p>
</li>
</ul>
</div>
<div class="checkout-cart-info-img"><img src="{{media url="wysiwyg/Checkout/cheout-cart-info-img.jpg"}}" alt="" width="220" /></div>
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
