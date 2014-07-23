<?php
/* @var $installer Gri_Cms_Model_Mysql4_Setup */
$installer = $this;
$installer->startSetup();

/* @var $block Mage_Cms_Model_Block */
$block = Mage::getModel('cms/block');
$data = array(
    array(
        'identifier' => 'safe_payment',
        'title' => 'Safe Payment',
        'is_active' => 1,
        'content' => '
<p><img src="{{skin_url=\'images/cart_lock.jpg\'}}" alt="Lock" width="28" height="32" /> SAFE AND SECURE PAYMENT <a href="3"><img src="{{skin_url=\'images/norton.jpg\'}}" alt="norton" width="60" height="36" /></a> <img src="{{skin_url=\'images/cart_d.jpg\'}}" alt="|" width="14" height="36" /> <a href="#"><img src="{{skin_url=\'images/visa.jpg\'}}" alt="vise" width="40" height="36" /></a> <a href="#"><img src="{{skin_url=\'images/master.jpg\'}}"}}" alt="master" width="40" height="36" /></a> <a href="#"><img src="{{skin_url=\'images/allpay.jpg\'}}" alt="alipay" width="42" height="36" /></a> <a href="#"><img src="{{skin_url=\'images/bank.jpg\'}}" alt="bank" width="82" height="36" /></a></p>
' ,
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


