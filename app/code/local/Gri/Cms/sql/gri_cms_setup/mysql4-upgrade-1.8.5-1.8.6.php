<?php
/* My Account Page Order Block */
/* @var $installer Gri_Cms_Model_Mysql4_Setup */
$installer = $this;
$installer->startSetup();


/* @var $block Mage_Cms_Model_Block */
$block = Mage::getModel('cms/block');
$data = array(
    array(
        'identifier' => 'acc_order_bottom_text',
        'title' => 'Account Page Order Bottom Text',
        'is_active' => 1,
        'content' => <<<EOT
<div class="bottom-text">
<div class="bottom-text-top">
<p>You can check your order status by logging into your personal account with CentralCentralShop.com at any time. If you need assistance, please don't hesitate to contact our Customer Care Team - we will be happy to serve you!</p>
</div>
<div class="bottom-text-bottom">
<p>Yours sincerely,</p>
<p>Customer Care Team</p>
<p>cs@centralcentralshop.com</p>
<p>+852-2480-2888</p>
</div>
</div>
EOT
        ,
    ),

    array(
        'identifier' => 'acc_order_customer_service',
        'title' => 'Account Page Order Customer Service',
        'is_active' => 1,
        'content' => <<<EOT
<div class="left-box">
<h4>THANK YOU FOR SHOPPING FOR US!</h4>
<p>Here are your order details. If you need assistance, please don't hesitate to contact our Customer Care Team - we will be happy to serve you!</p>
<a>CONTINUE SHOPPING</a></div>
<div class="right-box"><img src="{{media url="wysiwyg//Account/customer_service.png"}}" alt="" /></div>
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
