<?php
/* @var $installer Gri_Cms_Model_Mysql4_Setup */
$installer = $this;
$installer->startSetup();


$installer->endSetup();

$nodeTableName = $installer->getTable('gri_cms/hierarchy_node');

$installer
    ->getConnection()
    ->addColumn($nodeTableName, 'scope', array(
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length'    => '8',
        'comment'   => 'Scope: default|website|store',
        'nullable'  => false,
        'default'   => 'default',
    ));
$installer
    ->getConnection()
    ->addColumn($nodeTableName, 'scope_id', array(
        'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'comment'   => 'Scope Id',
        'nullable'  => false,
        'default'   => '0',
        'UNSIGNED'  => true,
    ));

$installer->endSetup();
