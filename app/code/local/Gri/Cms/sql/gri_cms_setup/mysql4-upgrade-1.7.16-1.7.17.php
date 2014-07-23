<?php
$installer = $this;
$installer->startSetup();
$block = Mage::getModel('cms/block')->load('new-arrivals');
$categoryIds = array(10,197,199,198,7,8,9,146,150,196,153);
/* @var $category Mage_Catalog_Model_Category */
$category = Mage::getModel('catalog/category');
$category->getResource()->getAttribute('display_mode')->setIsGlobal(1);
$category->getResource()->getAttribute('landing_page')->setIsGlobal(1);
$category->getResource()->getAttribute('available_sort_by')->setIsGlobal(1);
foreach($categoryIds as $id) {
	$category->unsetData();
	if ($category->setStoreId(0)->load($id)) {
		$category->setDisplayMode($category::DM_PAGE)->setLandingPage($block->getId());
		$category->save();
	}
}


$installer->endSetup();
