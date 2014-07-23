<?php

class ProxiBlue_DynCatProd_Model_Resource_Product_Collection extends Mage_Catalog_Model_Resource_Product_Collection {

    /**
     * Retreive clear select
     *
     * @return Varien_Db_Select
     */
    protected function _getClearSelect() {
        $select = parent::_getClearSelect();
        if ($this->hasFlag('is_dynamic')) {
            $select->reset(Zend_Db_Select::GROUP);
            $select->reset(Zend_Db_Select::DISTINCT);
        }
        return $select;
    }

    /**
     * Retrieve unique attribute set ids in collection
     *
     * @return array
     */
    public function getSetIds() {
        $select = clone $this->getSelect();
        /** @var $select Varien_Db_Select */
        $select->reset(Zend_Db_Select::COLUMNS);
        $select->distinct(true);
        $select->columns('attribute_set_id');
        if ($this->hasFlag('is_dynamic')) {
            $fromPart = $select->getPart(Zend_Db_Select::FROM);
            $select->reset(Zend_Db_Select::FROM);
            if (array_key_exists('price_index', $fromPart)) {
                unset($fromPart['price_index']);
            }
            if (array_key_exists('cat_index', $fromPart)) {
                unset($fromPart['cat_index']);
            }
            $select->setPart(Zend_Db_Select::FROM, $fromPart);
        }
        return $this->getConnection()->fetchCol($select);
    }

    public function load($printQuery = false, $logQuery = false) {
        if ($this->hasFlag('remove_cat_filter')) {
            $fromPart = $this->getSelect()->getPart(Zend_Db_Select::FROM);
            if (isset($fromPart['cat_index']) && isset($fromPart['cat_index']['joinCondition'])) {
                $joinParts = explode('AND', $fromPart['cat_index']['joinCondition']);
                foreach ($joinParts as $key => $part) {
                    if (strPos($part, $this->getFlag('remove_cat_filter')) > 0) {
                        unset($joinParts[$key]);
                    }
                }
                $fromPart['cat_index']['joinCondition'] = implode(' AND ', $joinParts);
                $this->getSelect()->setPart(Zend_Db_Select::FROM, $fromPart);
                $this->getSelect()->group('e.entity_id');
            }
        }
        return parent::load($printQuery, $logQuery);
    }

}
