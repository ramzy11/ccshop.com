<?php
/* @var $this Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();
/* @var $block Mage_Cms_Model_Block */
$block = Mage::getModel('cms/block');
$block->load($id = Gri_Sales_Helper_Data::BLOCK_PRODUCT_PROMOTION_RULE);
$block->setTitle('Product Promotion Rule Block')
    ->setIdentifier($id)
    ->setIsActive(1)
    ->setContent('<p>{{rule_name}}</p>')
    ->setStores(array(0))
    ->save()
;
$block->unsetData()->load($id = Gri_Sales_Helper_Data::BLOCK_PRODUCT_OPTIONAL_PROMOTION);
$block->setTitle('Product Optional Promotion Block')
    ->setIdentifier($id)
    ->setIsActive(1)
    ->setContent('<p>{{product_name}}</p>')
    ->setStores(array(0))
    ->save()
;

$installer->endSetup();
