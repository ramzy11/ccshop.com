<?php
/* @var $installer Gri_Cms_Model_Mysql4_Setup */
$installer = $this;
$installer->startSetup();

$nodeTableName = $installer->getTable('gri_cms/hierarchy_node');

$installer
    ->getConnection()
    ->dropIndex($nodeTableName, 'UNQ_REQUEST_URL');

$keyFieldsList = array('request_url', 'scope', 'scope_id');
$installer
    ->getConnection()
   // ->dropIndex($nodeTableName, 'UNQ_REQUEST_URL')
    ->addIndex(
        $nodeTableName,
        $installer->getIdxName(
            'gri_cms/hierarchy_node',
            $keyFieldsList,
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        $keyFieldsList,
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    );

$installer->endSetup();
