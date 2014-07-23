<?php
$installer = $this;
$installer->startSetup();
$category = Mage::getModel('catalog/category');
$category->getResource()->getAttribute('include_in_menu')->setIsGlobal(1);
$collection = $category->getCollection()->addAttributeToFilter('url_key', array('in'=>array('new-arrivals','editor','best-sellers','exclusives','shop-by-look')));
if($collection){
	foreach($collection as $v) {
		$v->setData('include_in_menu',0)->save();
	}
}
$installer->endSetup();