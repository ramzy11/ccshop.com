<?php
$installer = $this;
$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'fapiao', 'varchar(128) NULL');
?>
