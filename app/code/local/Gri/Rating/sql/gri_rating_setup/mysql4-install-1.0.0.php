<?php
/* @var $this Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$ratingModel = Mage::getModel('rating/rating');

$stores = array();
Mage::app()->reinitStores();
foreach (Mage::app()->getStores(TRUE) as $store) $stores[] = $store->getId();

$ratingModel->setRatingCode('Rating')
    ->setStores($stores)
    ->setId(1)
    ->setEntityId(1) /* 1 for product */
    ->save();

$installer->endSetup();
