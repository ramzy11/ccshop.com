<?php
  /* @var $installer Mage_Core_Model_Resource_Setup */
  $installer = $this;
  $installer->startSetup();

 /**
  * alter table 'magiatec_colorswatch_product'  add column `sort`
  */
  if ($this->tableExists($this->getTable('magiatecolorswatch/product'))) {
     $sql = "ALTER TABLE `{$installer->getTable('magiatecolorswatch/product')}` ADD COLUMN  `sort` int(10) UNSIGNED NOT NULL DEFAULT '0';";
     $installer->run($sql);
  }

$installer->endSetup();
