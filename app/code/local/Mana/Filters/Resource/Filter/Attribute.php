<?php
/**
 * @category    Mana
 * @package     Mana_Filters
 * @copyright   Copyright (c) http://www.manadev.com
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
/**
 * Resource type which contains sql code for applying filters and related operations
 * @author Mana Team
 * Injected instead of standard resource catalog/layer_filter_attribute in
 * Mana_Filters_Model_Filter_Attribute::_getResource().
 */
class Mana_Filters_Resource_Filter_Attribute extends Mage_Catalog_Model_Resource_Eav_Mysql4_Layer_Filter_Attribute {
    protected static $_appliedFilters = array();
    /**
     * Modifies product collection select sql to include only those products which conforms this filter's conditions
     * @param Mana_Filters_Model_Filter_Attribute $filter
     * @param string $value
     * This method is overridden by copying (method body was pasted from parent class and modified as needed). All
     * changes are marked with comments.
     * @see Mage_Catalog_Model_Resource_Eav_Mysql4_Layer_Filter_Attribute::applyFilterToCollection()
     */
    public function applyFilterToCollection($filter, $value)
	    {
			$collection = $filter->getLayer()->getProductCollection();
			// MANA BEGIN: prevent product to appear twice if it conforms joined codition 2 times (e.g. if product
	    	// has two values assigned for an attribute and both are filtered).
			$collection->getSelect()->distinct(true);
	    	// MANA END
			$attribute  = $filter->getAttributeModel();
            if (isset(self::$_appliedFilters[$attribute->getAttributeCode()])) {
                return $this;
            }
            self::$_appliedFilters[$attribute->getAttributeCode()] = TRUE;
	        $connection = $this->_getReadAdapter();
	        switch ($filter->getFilterOptions()->getOperation()) {
	            case '':
	                $tableAlias = $attribute->getAttributeCode() . '_idx';
	                $conditions = array(
	                    "{$tableAlias}.entity_id = e.entity_id",
	                    $connection->quoteInto("{$tableAlias}.attribute_id = ?", $attribute->getAttributeId()),
	                    $connection->quoteInto("{$tableAlias}.store_id = ?", $collection->getStoreId()),
	                    "{$tableAlias}.value in (" . implode(',', array_filter(explode('_', $value))) . ")"
	                );
	  				$totalCondition = join(' AND ', $conditions);
	  				// filter for color1 also search for color2
	                if(in_array($attribute->getAttributeCode(), array('color_filter_1','size_filter_1'))) {
						$secondAttribute = $attribute->getAttributeCode()== 'color_filter_1' ?
						Mage::getModel('eav/config')->getAttribute('catalog_product','color_filter_2')
						:Mage::getModel('eav/config')->getAttribute('catalog_product','size_filter_2');
						$firstAttributeLabels = array();
						foreach (explode('_', $value) as $sub_value) {
							$firstAttributeLabels[] = $attribute->getSource()->getOptionText($sub_value);
						}
						$secondAttributeValue = '';
						foreach($secondAttribute->getSource()->getAllOptions() as $option) {
							foreach ($firstAttributeLabels as $firstAttributeLabel) {
								if($option['label'] == $firstAttributeLabel) {
									if($secondAttributeValue !== "") $secondAttributeValue .=",";
									$secondAttributeValue .= $option['value'];
								}
							}
						}
						if($secondAttributeValue) {
		                	$secondAttributeCondition = array(
		                    "{$tableAlias}.entity_id = e.entity_id",
		                    $connection->quoteInto("{$tableAlias}.attribute_id = ?", $secondAttribute->getAttributeId()),
		                    $connection->quoteInto("{$tableAlias}.store_id = ?", $collection->getStoreId()),
		                    "{$tableAlias}.value in (" . implode(',', array_filter(explode('_', $secondAttributeValue))) . ")"
		              		);
		                	$totalCondition = "(".join(' AND ', $conditions) . ' OR ' . join(' AND ', $secondAttributeCondition) .")";
		                };
	                }
	                $collection->getSelect()
	                        ->distinct()
	                        ->join(
	                    array($tableAlias => $this->getMainTable()),
	                    $totalCondition,
	                    array()
	                );
	                break;
	            case 'and':
	                foreach (explode('_', $value) as $i => $singleValue) {
	                    $tableAlias = $attribute->getAttributeCode() . '_idx'.$i;
	                    $conditions = array(
	                        "{$tableAlias}.entity_id = e.entity_id",
	                        $connection->quoteInto("{$tableAlias}.attribute_id = ?", $attribute->getAttributeId()),
	                        $connection->quoteInto("{$tableAlias}.store_id = ?", $collection->getStoreId()),
	                        "{$tableAlias}.value = $singleValue"
	                    );

	                    // filter for color1 also search for color2
	                 if(in_array($attribute->getAttributeCode(), array('color_filter_1','size_filter_1'))) {
						$secondAttribute = $attribute->getAttributeCode()== 'color_filter_1' ?
						Mage::getModel('eav/config')->getAttribute('catalog_product','color_filter_2')
						:Mage::getModel('eav/config')->getAttribute('catalog_product','size_filter_2');
						$firstAttributeLabels = array();
						foreach (explode('_', $value) as $sub_value) {
							$firstAttributeLabels[] = $attribute->getSource()->getOptionText($sub_value);
						}
						$secondAttributeValue = '';
						foreach($secondAttribute->getSource()->getAllOptions() as $option) {
							foreach ($firstAttributeLabels as $firstAttributeLabel) {
								if($option['label'] == $firstAttributeLabel) {
									if($secondAttributeValue !== "") $secondAttributeValue .=",";
									$secondAttributeValue .= $option['value'];
								}
							}
						}
						if($secondAttributeValue) {
		                	$secondAttributeCondition = array(
		                    "{$tableAlias}.entity_id = e.entity_id",
		                    $connection->quoteInto("{$tableAlias}.attribute_id = ?", $secondAttribute->getAttributeId()),
		                    $connection->quoteInto("{$tableAlias}.store_id = ?", $collection->getStoreId()),
		                    "{$tableAlias}.value in (" . implode(',', array_filter(explode('_', $secondAttributeValue))) . ")"
		              		);
		                	$totalCondition = "(".join(' AND ', $conditions) . ' OR ' . join(' AND ', $secondAttributeCondition) .")";
		                };
	                }
	                    $collection->getSelect()
	                            ->distinct()
	                            ->join(
	                        array($tableAlias => $this->getMainTable()),
	                        $totalCondition,
	                        array()
	                    );
	                }
	                break;
	            default: throw new Exception('Not implemented');
		        }
		     return $this;
    }
    /**
     * For each option visible to person as a filter choice counts how many products are there given that all the
     * other filters are applied
     * @param Mana_Filters_Model_Filter_Attribute $filter
     * @return array Each entry in result is int option_id => int count
     * This method is overridden by copying (method body was pasted from parent class and modified as needed). All
     * changes are marked with comments.
     * @see Mage_Catalog_Model_Resource_Eav_Mysql4_Layer_Filter_Attribute::getCount()
     */
    public function getCount($filter)
    {
    	// clone select from collection with filters
        $select = clone $filter->getLayer()->getProductCollection()->getSelect();
        // reset columns, order and limitation conditions
        $select->reset(Zend_Db_Select::COLUMNS);
        $select->reset(Zend_Db_Select::ORDER);
        $select->reset(Zend_Db_Select::GROUP);
        $select->reset(Zend_Db_Select::LIMIT_COUNT);
        $select->reset(Zend_Db_Select::LIMIT_OFFSET);

        $connection = $this->_getReadAdapter();
        $attribute  = $filter->getAttributeModel();
       // if($attribute->getAttributeCode() == 'size') return $this->getSizeCount($filter);
        $tableAlias = $attribute->getAttributeCode() . '_idx';

		// MANA BEGIN: if there is already applied filter with the same name, then unjoin it from select.
		// TODO: comment on Mage::registry('mana_cat_index_from_condition') after we edit category filters
        $from = array();
		$catIndexCondition = Mage::registry('mana_cat_index_from_condition');
	    foreach ($select->getPart(Zend_Db_Select::FROM) as $key => $value) {
			if ($key != $tableAlias) {
				if ($catIndexCondition && ($catIndexCondition == $value['joinCondition'])) {
	        		$value['joinCondition'] = Mage::registry('mana_cat_index_to_condition');
				}
        		$from[$key] = $value;
			}
		}
		$select->setPart(Zend_Db_Select::FROM, $from);
		// MANA END
		if(in_array($attribute->getAttributeCode(), array('color_filter_1','size_filter_1'))) {
            $secondAttribute = $attribute->getAttributeCode() == 'color_filter_1' ?
                Mage::getModel('eav/config')->getAttribute('catalog_product', 'color_filter_2')
                : Mage::getModel('eav/config')->getAttribute('catalog_product', 'size_filter_2');
            $sub_condition = "(" . $connection->quoteInto("{$tableAlias}.attribute_id =? ", $attribute->getAttributeId()) .
                $connection->quoteInto("OR {$tableAlias}.attribute_id = ?", $secondAttribute->getAttributeId()) . ")";
            $conditions = array(
                "{$tableAlias}.entity_id = e.entity_id",
                $sub_condition,
                $connection->quoteInto("{$tableAlias}.store_id = ?", $filter->getStoreId()),
            );
            if ($attribute->getAttributeCode() == 'color_filter_1') {
                /* @var $flashSaleHelper Gri_FlashSale_Helper_Data */
                $flashSaleHelper = Mage::helper('gri_flashsale');
                if ($flashSaleHelper->getRemoveUnavailableOptions()) {
                    $select->where("{$tableAlias}.value IN (fp.color_filter_1, fp.color_filter_2)");
                }
            }
        } else {
			$conditions = array(
				"{$tableAlias}.entity_id = e.entity_id",
				$connection->quoteInto("{$tableAlias}.attribute_id = ?", $attribute->getAttributeId()),
				$connection->quoteInto("{$tableAlias}.store_id = ?", $filter->getStoreId()),
				);
		}
        $select
            ->join(
                array($tableAlias => $this->getMainTable()),
                join(' AND ', $conditions),
            	array('value','count'=>"COUNT(DISTINCT {$tableAlias}.entity_id)")
            	);
            if(in_array($attribute->getAttributeCode(), array('color_filter_1','size_filter_1'))) {
        	$tableAlias2 = 'attrbute_option';
        	$conditions = array(
        		"{$tableAlias2}.option_id = {$tableAlias}.value",
        		$connection->quoteInto("{$tableAlias2}.store_id = ?",0),
        		);
       		$select
	        	->join(
	        	array($tableAlias2 =>'eav_attribute_option_value'),
	        	join(' AND ', $conditions),
	        	array('label' => 'value')
	        		);
        }
        $groupValueTabel = isset($tableAlias2) ? $tableAlias2 :$tableAlias;
        $select->group("{$groupValueTabel}.value");
        return $connection->fetchPairs($select);
    }

    public function getSizeCount($filter)
    {

    	// clone select from collection with filters
    	$select = clone $filter->getLayer()->getProductCollection()->getSelect();
    	// reset columns, order and limitation conditions
    	$select->reset(Zend_Db_Select::COLUMNS);
    	$select->reset(Zend_Db_Select::ORDER);
    	$select->reset(Zend_Db_Select::GROUP);
    	$select->reset(Zend_Db_Select::LIMIT_COUNT);
    	$select->reset(Zend_Db_Select::LIMIT_OFFSET);
    	$connection = $this->_getReadAdapter();
    	$attribute  = $filter->getAttributeModel();
    	$tableAlias = $attribute->getAttributeCode() . '_idx';

    	// MANA BEGIN: if there is already applied filter with the same name, then unjoin it from select.
    	// TODO: comment on Mage::registry('mana_cat_index_from_condition') after we edit category filters
    	$from = array();
    	$catIndexCondition = Mage::registry('mana_cat_index_from_condition');
    	foreach ($select->getPart(Zend_Db_Select::FROM) as $key => $value) {
    		if ($key != $tableAlias) {
    			if ($catIndexCondition && ($catIndexCondition == $value['joinCondition'])) {
    				$value['joinCondition'] = Mage::registry('mana_cat_index_to_condition');
    			}
    			$from[$key] = $value;
    		}
    	}
    	$select->setPart(Zend_Db_Select::FROM, $from);
    	// MANA END
    	$conditions = array(
    	"{$tableAlias}.entity_id = e.entity_id",
    	$connection->quoteInto("{$tableAlias}.attribute_id = ?", $attribute->getAttributeId()),
    	$connection->quoteInto("{$tableAlias}.store_id = ?", $filter->getStoreId()),
    	);
    		$select
    		->join(
    		array($tableAlias => $this->getMainTable()),
    	join(' AND ', $conditions),
    	array('')
    	);
    	$tableAlias2 = 'attribute_option';
    	$conditions = array(
    	"{$tableAlias2}.option_id = {$tableAlias}.value",
    	$connection->quoteInto("{$tableAlias2}.store_id = ?",0),
    	);
    	$select
	    	->join(
	    	array($tableAlias2 =>'eav_attribute_option_value'),
	    	join(' AND ', $conditions),
	    	array('')
	    	);
    	$select->joinLeft(
    		array('size_mapping'),
    		"size_mapping.admin_size=attribute_option.value",
    		array("{$tableAlias2}.value",'count'=>"COUNT(DISTINCT {$tableAlias}.entity_id)",'admin_size','universal_size')
    		);
    	$groupValueTabel = isset($tableAlias2) ? $tableAlias2 :$tableAlias;
    	$select->group("size_mapping.universal_size");
    	return $connection->fetchPairs($select);

    }

}

class FilterSingleton {

	static private $instance;

	public $return = null;

	private function __construct() {

	}

	static public function singleton() {
		if (!isset(self::$instance)) {
			$c = __CLASS__;
			self::$instance = new $c;
		}

		return self::$instance;
	}
}
