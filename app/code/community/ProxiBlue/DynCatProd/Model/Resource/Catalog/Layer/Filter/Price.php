<?php

/**
 * Catalog Layer Price Filter resource model
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class ProxiBlue_DynCatProd_Model_Resource_Catalog_Layer_Filter_Price extends Mana_Filters_Resource_Filter_Price {

    /**
     * Retrieve clean select with joined price index table
     *
     * @param Mage_Catalog_Model_Layer_Filter_Price $filter
     * @return Varien_Db_Select
     */
    protected function _getSelect($filter) {

        $collection = $filter->getLayer()->getProductCollection();
        if ($collection->hasFlag('is_dynamic')) {
            // temporarily disable the GROUP / DISTINCT on the collection to make count work.
            if (!is_null($collection->getCatalogPreparedSelect())) {
                $select = $collection->getCatalogPreparedSelect();
            } else {
                $select = $collection->getSelect();
            }
            $select->reset(Zend_Db_Select::GROUP);
            $select->reset(Zend_Db_Select::DISTINCT);
            //$wherePart = $select->getPart(Zend_Db_Select::WHERE);
            //unset($wherePart[0]);
            //$select->setPart(Zend_Db_Select::WHERE, array());
        }

        $result = parent::_getSelect($filter);
        if ($collection->hasFlag('is_dynamic')) {
            // PUT BACK THE GROUP AND DISTINCT CLAUSES
            $select->distinct(true);
            $select->group('e.entity_id');
        }

        return $result;
    }

    /**
     * Retrieve array with products counts per price range
     *
     * @param Mage_Catalog_Model_Layer_Filter_Price $filter
     * @param int $range
     * @return array
     */
    public function getCount($filter, $range) {
        $select = $this->_getSelect($filter);
        $collection = $filter->getLayer()->getProductCollection();
        if ($collection->hasFlag('remove_cat_filter')) {
            $fromPart = $select->getPart(Zend_Db_Select::FROM);
            $select->reset(Zend_Db_Select::FROM);
            if(array_key_exists('cat_index', $fromPart)){
                unset($fromPart['cat_index']);
            }
            $select->setPart(Zend_Db_Select::FROM,$fromPart);
        }

        $priceExpression = $this->_getFullPriceExpression($filter, $select);

        /**
         * Check and set correct variable values to prevent SQL-injections
         */
        $range = floatval($range);
        if ($range == 0) {
            $range = 1;
        }
        $countExpr = new Zend_Db_Expr('COUNT(*)');
        $rangeExpr = new Zend_Db_Expr("FLOOR(({$priceExpression}) / {$range}) + 1");

        $select->columns(array(
            'range' => $rangeExpr,
            'count' => $countExpr
        ));
        $select->group($rangeExpr)->order("$rangeExpr ASC");

        // Fixed bug Column not found: 1054 Unknown column 'e.type_id' in 'where clause'
        // Fixed bug Column not found: 1054 Unknown column 'e.attribute_set_id' in 'where clause'
        $where = $select->getPart('where');
        foreach ($where as &$v) {
            if (strpos($v, 'e.type_id')) $v = NULL;
            if (strpos($v, 'attribute_set_id')) $v = NULL;
        }
        unset($v);
        $where = array_values(array_filter($where));
        if (isset($where[0]) && substr($where[0], 0, 4) == 'AND ') $where[0] = substr($where[0], 4);
        $select->setPart('where', $where);
        return $this->_getReadAdapter()->fetchPairs($select);
    }

}
