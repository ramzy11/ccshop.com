<?php
$installer = $this;
$installer->startSetup();

/**
 * Create table 'magiatec_colorswatch_product'
 */
if (!$this->tableExists($this->getTable('magiatecolorswatch/product'))) {
    $table = $installer->getConnection()
        ->newTable($installer->getTable('magiatecolorswatch/product'))
        ->addColumn('image_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
            ), 'Image ID')
        ->addColumn('product_super_attribute_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned'  => true,
            'nullable'  => false,
            'default'   => '0',
            ), 'Product Super Attribute ID')
        ->addColumn('value_index', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            'nullable'  => true,
            'default'   => null,
            ), 'Value Index')
        ->addColumn('image', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            ), 'Image')
        ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'unsigned'  => true,
            'nullable'  => false,
            'default'   => '0',
            ), 'Store ID')
        ->addIndex($installer->getIdxName('magiatecolorswatch/product', array('product_super_attribute_id')),
            array('product_super_attribute_id'))
        ->addIndex($installer->getIdxName('magiatecolorswatch/product', array('store_id')),
            array('store_id'))
        ->addForeignKey(
            $installer->getFkName(
                'magiatecolorswatch/product',
                'store_id',
                'core_store',
                'store_id'
            ),
            'store_id', $installer->getTable('core/store'), 'store_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
        ->addForeignKey(
            $installer->getFkName(
                'magiatecolorswatch/product',
                'product_super_attribute_id',
                'catalog/product_super_attribute_label',
                'product_super_attribute_id'
            ),
            'product_super_attribute_id',
            $installer->getTable('catalog/product_super_attribute_label'),
            'product_super_attribute_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
        ->setComment('Super Attribute Images Table');
    $installer->getConnection()->createTable($table);
}
/**
 * Create table 'magiatec_colorswatch_attribute'
 */
if (!$this->tableExists($this->getTable('magiatecolorswatch/attribute'))) {
    $table = $installer->getConnection()
        ->newTable($installer->getTable('magiatecolorswatch/attribute'))
        ->addColumn('image_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
            ), 'Image ID')
        ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'unsigned'  => true,
            'nullable'  => false,
            'default'   => '0',
            ), 'Attribute Id')
        ->addColumn('option_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned'  => true,
            'nullable'  => false,
            'default'   => '0',
            ), 'Option Id')
        ->addColumn('image', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            ), 'Image')
        ->addIndex($installer->getIdxName('magiatecolorswatch/attribute', array('attribute_id')),
            array('attribute_id'))
        ->addIndex($installer->getIdxName('magiatecolorswatch/attribute', array('option_id')),
            array('option_id'))
        ->addForeignKey($installer->getFkName('magiatecolorswatch/attribute', 'attribute_id', 'eav/attribute', 'attribute_id'),
            'attribute_id', $installer->getTable('eav/attribute'), 'attribute_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
        ->addForeignKey(
            $installer->getFkName('magiatecolorswatch/attribute', 'option_id', 'eav/attribute_option', 'option_id'),
            'option_id', $installer->getTable('eav/attribute_option'), 'option_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
        ->setComment('Attribute Option Images Table');

    $installer->getConnection()->createTable($table);
}

$installer->endSetup();
