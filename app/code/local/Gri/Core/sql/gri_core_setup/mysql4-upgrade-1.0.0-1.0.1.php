<?php
/* @var $this Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$configTable = $installer->getTable('core/config_data');
$sql = "UPDATE `{$configTable}` SET `value` = '&copy; 2012 Central/Central. All rights reserved.'
WHERE `path` = 'design/footer/copyright';";
$installer->run($sql);

$installer->endSetup();
