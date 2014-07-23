<?php

class Gri_CatalogCustom_Model_Resource_Product_Indexer_Price_Grouped
    extends Mage_Catalog_Model_Resource_Product_Indexer_Price_Grouped
{

    protected function _prepareGroupedProductPriceData($entityIds = null)
    {
        $write = $this->_getWriteAdapter();
        $table = $this->getIdxTable();

        $select = $write->select()
            ->from(array('e' => $this->getTable('catalog/product')), 'entity_id')
            ->joinLeft(
            array('l' => $this->getTable('catalog/product_link')),
            'e.entity_id = l.product_id AND l.link_type_id=' . Mage_Catalog_Model_Product_Link::LINK_TYPE_GROUPED,
            array())
            ->join(
            array('cg' => $this->getTable('customer/customer_group')),
            '',
            array('customer_group_id'));
        $this->_addWebsiteJoinToSelect($select, true);
        $this->_addProductWebsiteJoinToSelect($select, 'cw.website_id', 'e.entity_id');
        $minCheckSql = $write->getCheckSql('le.required_options = 0', 'i.min_price', 0);
        $maxCheckSql = $write->getCheckSql('le.required_options = 0', 'i.max_price', 0);
        $select->columns('website_id', 'cw')
            ->joinLeft(
            array('le' => $this->getTable('catalog/product')),
            'le.entity_id = l.linked_product_id',
            array())
            ->joinLeft(
            array('i' => $table),
            'i.entity_id = l.linked_product_id AND i.website_id = cw.website_id'
                . ' AND i.customer_group_id = cg.customer_group_id',
            array(
                'tax_class_id' => $this->_getReadAdapter()
                    ->getCheckSql('MIN(i.tax_class_id) IS NULL', '0', 'MIN(i.tax_class_id)'),
                'price'        => new Zend_Db_Expr('NULL'),
                'final_price'  => new Zend_Db_Expr('NULL'),
                'min_price'    => new Zend_Db_Expr('i.min_price'),
                'max_price'    => new Zend_Db_Expr('i.max_price'),
                'tier_price'   => new Zend_Db_Expr('NULL'),
                'group_price'  => new Zend_Db_Expr('NULL'),
            ))
            ->group(array('e.entity_id', 'cg.customer_group_id', 'cw.website_id'))
            ->where('e.type_id=?', $this->getTypeId());

        if (!is_null($entityIds)) {
            $select->where('l.product_id IN(?)', $entityIds);
        }

        /**
         * Add additional external limitation
         */
        Mage::dispatchEvent('catalog_product_prepare_index_select', array(
            'select'        => $select,
            'entity_field'  => new Zend_Db_Expr('e.entity_id'),
            'website_field' => new Zend_Db_Expr('cw.website_id'),
            'store_field'   => new Zend_Db_Expr('cs.store_id')
        ));

        $query = $select->insertFromSelect($table);
        $write->query($query);

        return $this;
    }
}
