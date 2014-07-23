<?php
/* @var $installer Gri_Cms_Model_Mysql4_Setup */
$installer = $this;
$installer->startSetup();
/* @var $block Mage_Cms_Model_Block */
$block = Mage::getModel('cms/block');
$data = array(
	array(
		'identifier' => 'new-arrivals',
		'content' => <<<EOT
<p>{{block type="catalog/product_list" name="product_list" as="product_list" template="catalog/product/list.phtml" }}</p>
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


