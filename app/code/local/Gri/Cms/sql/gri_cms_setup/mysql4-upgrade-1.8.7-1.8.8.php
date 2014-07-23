<?php
/* @var $installer Gri_Cms_Model_Mysql4_Setup */
$installer = $this;
$installer->startSetup();

/* @var $block Mage_Cms_Model_Block */
$block = Mage::getModel('cms/block');

$content = <<<EOT
<p>By creating an account with our store, you will be able to move through the checkout process faster, store multiple shipping addresses, view and track your orders in your account and more</p>
EOT;
$data = array (
    'identifier' => 'new-customer-remarks',
    'title' => 'New Customer Remarks',
    'is_active' => 1,
    'content' => $content
);
$block->setData($data);
$block->setStores(array(0))->save();

$installer->endSetup();
