<?php
/* @var $installer Gri_Cms_Model_Mysql4_Setup */
$installer = $this;
$installer->startSetup();

/* @var $block Mage_Cms_Model_Block */
$block = Mage::getModel('cms/block');

$data = array(
    array (
        'identifier' => 'my_account_my_order_thank_you',
        'title' => 'Thank You For Shipping With Us',
        'is_active' => 1,
        'content' => '<div class="my-orders-thank-you">
    <div class="thank-you-left">
        <h3>THANK YOU FOR SHIPPING WITH US!</h3>
        <p>Here are your order details. If you need assistance,please don\'t hesitate to contact our Customer Care Team - we will be happy to serve you!</p>
        <div class="clear"></div>
        <a class="continue-shopping href="/">Continue Shopping</a>
    </div>
    <div class="thank-you-right">
        <img src="{{skin_url=\'images/customer-service.png\'}}" />
    </div>
    <div class="clear"></div>
</div>'
    )
);
foreach ($data as $_data) {
    $block->unsetData()->load($_data['identifier'], 'identifier');
    if (!$block->getId()) {
        $block->setIdentifier($_data['identifier']);
        $block->setTitle($_data['title']);
        $block->setStores(array(0));
    }

    $block->setContent($_data['content']);
    $block->save();
}

$installer->endSetup();
