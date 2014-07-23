<?php

class Gri_FlashSale_Model_Resource_Product_Collection extends Mage_Catalog_Model_Resource_Product_Collection
{

    protected function _buildClearSelect($select = null)
    {
        $select = parent::_buildClearSelect($select);
        $select->reset(Zend_Db_Select::GROUP);
        return $select;
    }

    protected function _productLimitationPrice($joinLeft = FALSE)
    {
        $filters = $this->_productLimitationFilters;
        if (empty($filters['use_price_index'])) {
            return $this;
        }

        /* @var $helper Mage_Core_Model_Resource_Helper_Mysql4 */
        $helper     = Mage::getResourceHelper('core');
        $connection = $this->getConnection();
        $select     = $this->getSelect();
        $joinCond   = implode(' AND ', array(
            'price_index.entity_id = e.entity_id',
            $connection->quoteInto('price_index.website_id = ?', $filters['website_id']),
            $connection->quoteInto('price_index.customer_group_id = ?', $filters['customer_group_id'])
        ));

        $fromPart = $select->getPart(Zend_Db_Select::FROM);
        if (!isset($fromPart['price_index'])) {
            $colls = array('price');
            $tableName = array('price_index' => $this->getTable('catalog/product_index_price'));
            if ($joinLeft) {
                $select->joinLeft($tableName, $joinCond, $colls);
            } else {
                $select->join($tableName, $joinCond, $colls);
            }
            // Set additional field filters
            foreach ($this->_priceDataFieldFilters as $filterData) {
                $select->where(call_user_func_array('sprintf', $filterData));
            }
        } else {
            $fromPart['price_index']['joinCondition'] = $joinCond;
            $select->setPart(Zend_Db_Select::FROM, $fromPart);
        }
        //Clean duplicated fields
        $helper->prepareColumnsList($select);

        return $this;
    }
}
