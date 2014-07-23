<?php
  //class ProxiBlue_DynCatProd_Model_Resource_Catalog_Layer_Filter_Attribute extends Mage_Catalog_Model_Resource_Layer_Filter_Attribute
  class ProxiBlue_DynCatProd_Model_Resource_Catalog_Layer_Filter_Attribute extends Mana_Filters_Resource_Filter_Attribute
  {

    protected static $_appliedFilters = array();

    /**
     * Retrieve array with products counts per attribute option
     *
     * @param Mage_Catalog_Model_Layer_Filter_Attribute $filter
     * @return array
     */
    public function getCount($filter) {
        $collection = $filter->getLayer()->getProductCollection();
        if ($collection->hasFlag('remove_cat_filter')) {
                $collection = Mage::helper('dyncatprod')->removeCatFilter($collection);
                $collection = Mage::helper('dyncatprod')->removeIndexTables($collection);
        }         
        if ($collection->hasFlag('is_dynamic')) {
            // temporarily disable the GROUP / DISTINCT on the collection to make count work.
            $collection->getSelect()->reset(Zend_Db_Select::GROUP);
            $collection->getSelect()->reset(Zend_Db_Select::DISTINCT);
        }
        $result = parent::getCount($filter);
        if ($collection->hasFlag('is_dynamic')) {
            // PUT BACK THE GROUP AND DISTINCT CLAUSES
            $collection->getSelect()->distinct(true);
            $collection->getSelect()->group('e.entity_id');
        }
        
        return $result;
    }
}
