<?php
/* @var $installer Gri_Cms_Model_Mysql4_Setup */
$installer = $this;
$installer->startSetup();

/* @var $block Mage_Cms_Model_Block */
$block = Mage::getModel('cms/block');

$data = array(
    array (
        'identifier' => 'my-newsletter-subscribed',
        'title' => 'My Newsletter Subscribed[CH]',
        'content' => '<div class="thank-you-for-subscription">
                <div class="left">
                    <h2>多謝您訂閱我們的電子報！</h2>
                    <p>您將會接收到我們的最新的推廣、新到貨品及潮流速遞！<br /> <br /> 如果您不想再收到我們任何的電子報，請點擊以下按鈕。</p>
                </div>
                <div class="right"><img src="{{skin_url=\'images/customer-service.png\'}}" alt="" /></div>
                </div>'
    ),
    array (
        'identifier' => 'my-newsletter-unsubscribed',
        'title' => 'My Newsletter Unsubscribed[CH]',
        'content' => '<div class="thank-you-for-subscription">
            <div class="left">
                <h2>您已經成功取消訂閱我們的電子報。多謝支持！</h2>
                <p>
                    <br />
                    <br />
                    如果您想再次收到我們任何的電子報，請點擊以下按鈕。
                </p>
            </div>
            <div class="right">
                <img src="{{skin_url=\'images/customer-service.png\'}}"/>
            </div>
        </div>'
    ),
);
foreach ($data as $_data) {
    $block->unsetData()
              ->setStores(array(1))
              ->load($_data['identifier'] , 'identifier');

    $block->setContent($_data['content']);
    $block->save();
}

$installer->endSetup();
