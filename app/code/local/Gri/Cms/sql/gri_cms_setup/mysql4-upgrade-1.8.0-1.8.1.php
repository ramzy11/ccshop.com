<?php
/* @var $installer Gri_Cms_Model_Mysql4_Setup */
$installer = $this;
$installer->startSetup();

/* @var $block Mage_Cms_Model_Block */
$block = Mage::getModel('cms/block');
 $data =
     array (
         array
         (
             'identifier'=> 'shop_new_in',
             'title'=> 'Shop New In',
             'is_active'=> 1,
             'content'=> '{{block type="gri_catalogcustom/home_shopNewIn" name="shop_new_in"}}',
         ),
         array
         (
             'identifier'=> 'most_recent_views',
             'title'=> 'Most Recent Views',
             'is_active'=> 1,
             'content'=> '{{block type="gri_catalogcustom/home_mostRecentViews" name="most_recent_views"}}',
         )
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
