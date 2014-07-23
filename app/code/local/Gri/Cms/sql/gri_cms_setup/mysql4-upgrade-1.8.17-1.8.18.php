<?php
/* @var $installer Gri_Cms_Model_Mysql4_Setup */
$installer = $this;
$installer->startSetup();

$installer->getConnection()
    ->addColumn(
        $installer->getTable('gri_cms/hierarchy_metadata'),
        'top_menu_visibility',
        array(
            'type'     => Varien_Db_Ddl_Table::TYPE_SMALLINT,
            'comment'  => 'Top Menu Visibility',
            'nullable' => true,
            'default'  => null,
            'unsigned' => true,
        )
    );

$installer->getConnection()
    ->addColumn(
        $installer->getTable('gri_cms/hierarchy_metadata'),
        'top_menu_excluded',
        array(
            'type'     => Varien_Db_Ddl_Table::TYPE_SMALLINT,
            'comment'  => 'Top Menu Excluded',
            'nullable' => true,
            'default'  => null,
            'unsigned' => true,
        )
    );

$installer->endSetup();
