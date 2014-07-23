<?php
/* @var $installer Gri_Cms_Model_Mysql4_Setup */
$installer = $this;
$installer->startSetup();


/* @var $block Mage_Cms_Model_Block */
$block = Mage::getModel('cms/block');
$data = array(
    array(
        'identifier' => 'bottom_contact_block',
        'title' => 'Bottom Contact us Block',
        'is_active' => 1,
        'content' => <<<EOT
<h3>LEAVE US A MESSAGE</h3>
<p>Feel free to leave us a message and we'll get back to you as soon as we can.</p>
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
