<?php


class ProxiBlue_DynCatProd_Model_Resource_Catalog_Layer_Filter_Decimal extends Mage_Catalog_Model_Resource_Layer_Filter_Decimal
{

    /**
     * Retrieve clean select with joined index table
     * Joined table has index
     *
     * @param Mage_Catalog_Model_Layer_Filter_Decimal $filter
     * @return Varien_Db_Select
     */
    protected function _getSelect($filter)
    {
        $collection = $filter->getLayer()->getProductCollection();
        if ($collection->hasFlag('is_dynamic')) {
            // temporarily disable the GROUP / DISTINCT on the collection to make count work.
            $collection->getSelect()->reset(Zend_Db_Select::GROUP);
            $collection->getSelect()->reset(Zend_Db_Select::DISTINCT);
        }
        $result = parent::_getSelect($filter);
        if ($collection->hasFlag('is_dynamic')) {
            // PUT BACK THE GROUP AND DISTINCT CLAUSES
            $collection->getSelect()->distinct(true);
            $collection->getSelect()->group('e.entity_id');
            
        }
        return $result;
    }

    
}
