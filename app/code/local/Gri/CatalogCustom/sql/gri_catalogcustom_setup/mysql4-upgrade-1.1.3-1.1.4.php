<?php
/* @var $this Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
$installer = $this;
$installer->startSetup();

$installer->updateAttribute('catalog_product', 'editors_pick', array(
    'backend_type' => 'int',
    'used_for_sort_by' => 1,
));
$installer->endSetup();
