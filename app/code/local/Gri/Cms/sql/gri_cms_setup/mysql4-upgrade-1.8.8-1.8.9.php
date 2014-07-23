<?php
/* @var $installer Gri_Cms_Model_Mysql4_Setup */
$installer = $this;
$installer->startSetup();

/* @var $block Mage_Cms_Model_Block */
$block = Mage::getModel('cms/block');

$data = array (
     array (
         'identifier' => 'most_recent_views',
         'content' => '{{widget type="reports/product_widget_viewed" page_size="10" template="reports/widget/viewed/content/viewed_list_slide.phtml"}}'
     ),
     array (
         'identifier' => 'shop_new_in',
         'content' => '{{widget type="catalog/product_widget_new" products_count="10" template="catalog/product/widget/new/content/new_list_slide.phtml"}}'
     )
);
foreach($data as $_data){
    $block->unsetData()->load($_data['identifier'],'identifier');
    if(!$block->getId()){
        $block->setIdentifier($_data['identifier']);
        $block->setStores(array(0));
    }
    $block->setContent($_data['content']);
    $block->save();
}
$installer->endSetup();
