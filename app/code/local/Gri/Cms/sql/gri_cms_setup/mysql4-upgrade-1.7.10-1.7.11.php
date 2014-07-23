<?php
/* @var $installer Gri_Cms_Model_Mysql4_Setup */
$installer = $this;
$installer->startSetup();

/* @var $block Mage_Cms_Model_Block */
$block = Mage::getModel('cms/block');
$data = array(
    array(
        'identifier' => 'checkout-package',
        'title' => 'Checkout Package',
        'is_active' => 1,
        'content' => <<<EOT
        <div class="con_title">
            <span class="rig_space">Package</span>
        </div>
        <div class="pay_met ship_table">
            <table width="100%" cellspacing="0 " cellpadding="0" border="0">
                <tbody><tr>
                    <td width="200"><img width="177" height="236" alt="bag" src="{{skin_url="images/ship_bag.jpg"}}"></td>
                    <td class="ship_bag"><strong>Central / Central Shopping Bag</strong>
                        <p>Package information</p></td>
                </tr>
                </tbody></table>
            <div class="clear"></div>
        </div>
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
