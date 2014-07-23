<?php
/* @var $installer Gri_Cms_Model_Mysql4_Setup */
$installer = $this;
$installer->startSetup();

/* @var $block Mage_Cms_Model_Block */
$block = Mage::getModel('cms/block');

$stores = array(1, 2);
$data = array(
    array (
    'identifier' => 'my-newsletter-subscribed',
    'title' => 'My Newsletter Subscribed',
    'is_active' => 1,
    'content' => '<div class="thank-you-for-subscription">
            <div class="left">
                <h2>Thank you for your subscribing To Our Newsletter</h2>
                <p>
                    You\'ll now receive exclusive promotions,latest arrivals and the hottest trends all in your in box!
                    <br />
                    <br />
                    If you change your mind and would like to not continue receiving our latest news and promotion,please contact us to unsubscribe at any time.
                </p>
            </div>
            <div class="right">
                <img src="{{skin_url=\'images/customer-service.png\'}}"/>
            </div>
        </div>'
    ),
    array (
        'identifier' => 'my-newsletter-unsubscribed',
        'title' => 'My Newsletter Unsubscribed',
        'is_active' => 1,
        'content' => '<div class="thank-you-for-subscription">
            <div class="left">
                <h2>You can subscribe Our Newsletter To get Latest News</h2>
                <p>
                    You\'ll now receive exclusive promotions,latest arrivals and the hottest trends all in your in box!
                    <br />
                    <br />
                    If you change your mind and would like to not continue receiving our latest news and promotion,please contact us to unsubscribe at any time.
                </p>
            </div>
            <div class="right">
                <img src="{{skin_url=\'images/customer-service.png\'}}"/>
            </div>
        </div>'
    ),
);
foreach ($data as $_data) {
    foreach($stores as $storeId) {
        $block->unsetData();
        $block->setStores(array($storeId));

        $title = $storeId == 1 ?  $_data['title'].'[CH]' : $_data['title'];

        $block->setIdentifier($_data['identifier']);
        $block->setTitle($title);
        $block->setContent($_data['content']);
        $block->save();
    }
}

$installer->endSetup();
