<?php
$installer = $this;
$installer->startSetup();
$category = Mage::getModel('catalog/category');
$category->getResource()->getAttribute('include_in_menu')->setIsGlobal(1);
$newArrivalCategory = $category->load(10);
if($newArrivalCategory) $newArrivalCategory->setData('include_in_menu',0)->save();
$installer->endSetup();