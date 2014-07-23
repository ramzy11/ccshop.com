<?php
/* @var $this Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();
ob_start();
readgzfile(dirname(__FILE__) . DS . 'gri_core_ip_to_country.sql.gz');
$sql = ob_get_contents();
ob_end_clean();
$installer->run($sql);

$installer->endSetup();
