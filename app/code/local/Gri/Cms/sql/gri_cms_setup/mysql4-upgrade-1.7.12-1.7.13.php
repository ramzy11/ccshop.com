<?php
/* @var $installer Gri_Cms_Model_Mysql4_Setup */
$installer = $this;
$installer->startSetup();

/* @var $block Mage_Cms_Model_Block */
$block = Mage::getModel('cms/block');
$data = array(
	array(
		'identifier' => 'vip-grading',
		'title' => 'VIP Grading',
		'is_active' => 1,
		'content' => <<<EOT
<div class="das_tb_tit">VIP Grading</div>
<div class="das_tit_btn"><a href="#">Terms &amp; Condition</a></div>
<div class="request_info">
<div class="con_table">
<div class="padding_20 no_padding">
<table style="width: 100%;" border="0" cellspacing="0 " cellpadding="0">
<tbody>
<tr>
<td style="text-align: center;" width="275"><img src="{{skin_url='images/vip_silver.jpg'}}" alt="VIP Silver" width="250" /></td>
<td>
<table class="vip_table" style="width: 100%;" border="0" cellspacing="0 " cellpadding="0">
<tbody>
<tr>
<td class="vip_sil_tit" colspan="2" nowrap="nowrap">Online VIP: SILVER</td>
</tr>
<tr>
<td nowrap="nowrap">
<p>Benefit:</p>
<ul class="silver_check">
<li>Shopping Discount: 15% Off</li>
<li>Earn Bonus Points</li>
<li>Free Gift Redemption</li>
<li>Buy Special Products</li>
<li>Join Pre-Order Activities</li>
</ul>
</td>
<td nowrap="nowrap">
<p>Upgrade Requirment:<br /> <strong>Spend HK$2,000 in 1 day<br /><span style="margin-left: 65px; font-weight: bold;">or</span><br /> Earn 3,000 points in 3 months</strong><br /><br /></p>
<p>Annual Renewal Requirment:<br /> <strong>Earn 3,000 points in a year</strong></p>
</td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>
<div class="clear bot_space">&nbsp;</div>
<table style="width: 100%;" border="0" cellspacing="0 " cellpadding="0">
<tbody>
<tr>
<td style="text-align: center;" width="275"><img src="{{skin_url='images/vip_gold.jpg'}}" alt="VIP Gold" width="250" /></td>
<td>
<table class="vip_table" style="width: 100%;" border="0" cellspacing="0 " cellpadding="0">
<tbody>
<tr>
<td class="vip_gol_tit" colspan="2" nowrap="nowrap">Online VIP: GOLD</td>
</tr>
<tr>
<td nowrap="nowrap">
<p>Benefit:</p>
<ul class="gold_check">
<li>Free Shipping</li>
<li>Shopping Discount: 15% Off</li>
<li>Earn Bonus Points</li>
<li>Free Gift Redemption</li>
<li>Buy Special Products</li>
<li>Join Pre-Order Activities</li>
</ul>
</td>
<td nowrap="nowrap">
<p>Upgrade Requirment:<br /> <strong>Spend HK$2,000 in 1 day<br /><span style="margin-left: 65px; font-weight: bold;">or</span><br /> Earn 3,000 points in 3 months </strong></p>
<br />
<p>Annual Renewal Requirment:<br /> <strong>Earn 3,000 points in a year</strong></p>
</td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>
<div class="clear bot_space_m">&nbsp;</div>
</div>
</div>
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