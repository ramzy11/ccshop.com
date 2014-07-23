<?php
/* @var $this Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

/* @var $ratingOptionModel Mage_Rating_Model_Rating_Option */
$ratingOptionModel = Mage::getModel('rating/rating_option');

for ($i = 1; $i <= 5; ++$i) {
    $ratingOptionModel->unsetData();
    $ratingOptionModel->load($i)
        ->setValue($i)
        ->save();
}

$installer->endSetup();
