<?php

class Gri_Reports_Model_Resource_Category_Tree extends Mage_Catalog_Model_Resource_Category_Tree
{
    protected $_baseCategories = array(
        'shoes',
        'clothing',
        'accessories',
    );
    protected $_dummyCategories = array(
        'new-arrivals',
        'best-sellers',
        'editor',
        'exclusives',
        'pre-order',
        'pre-sales',
    );

    protected function _createCollectionDataSelect($sorted = TRUE, $optionalAttributes = array())
    {
        $select = parent::_createCollectionDataSelect($sorted, $optionalAttributes);
        $this->_hideSpecialCategories($select);
        return $select;
    }

    protected function _hideSpecialCategories(Zend_Db_Select $select, $mainTable = 'e')
    {
        if (Mage::registry('hide_special_categories')) {
            $this->_joinAttributeToSelect('url_key', $select, $mainTable);
            $adapter = $select->getAdapter();
            $fieldUrlKey = $adapter->quoteIdentifier('d_url_key.value');
            $fieldLevel = $adapter->quoteIdentifier($mainTable . '.level');
            $valuesBaseCategories = $adapter->quoteInto('?', $this->_baseCategories);
            $valuesDummyCategories = $adapter->quoteInto('?', $this->_dummyCategories);
            $select->where("
{$fieldLevel} < 2 OR (
    {$fieldUrlKey} IN ({$valuesBaseCategories}) AND {$fieldLevel} = 2
) OR (
    {$fieldUrlKey} NOT IN ({$valuesDummyCategories}) AND {$fieldLevel} > 2
)");
        }
        return $this;
    }

    protected function _joinAttributeToSelect($attributeCode, Zend_Db_Select $select, $mainTable = 'e')
    {
        /* @var $attribute Mage_Eav_Model_Entity_Attribute */
        $attribute = Mage::getResourceSingleton('catalog/category')->getAttribute($attributeCode);
        // join non-static attribute table
        if (!$attribute->getBackend()->isStatic()) {
            $tableDefault   = sprintf('d_%s', $attributeCode);
            $tableStore     = sprintf('s_%s', $attributeCode);
            $valueExpr      = $this->_conn
                ->getCheckSql("{$tableStore}.value_id > 0", "{$tableStore}.value", "{$tableDefault}.value");

            $select
                ->joinLeft(
                    array($tableDefault => $attribute->getBackend()->getTable()),
                    sprintf('%1$s.entity_id=%4$s.entity_id AND %1$s.attribute_id=%2$d'
                            . ' AND %1$s.entity_type_id=%4$s.entity_type_id AND %1$s.store_id=%3$d',
                        $tableDefault, $attribute->getId(), Mage_Core_Model_App::ADMIN_STORE_ID, $mainTable),
                    array($attributeCode => 'value'))
                ->joinLeft(
                    array($tableStore => $attribute->getBackend()->getTable()),
                    sprintf('%1$s.entity_id=%4$s.entity_id AND %1$s.attribute_id=%2$d'
                            . ' AND %1$s.entity_type_id=%4$s.entity_type_id AND %1$s.store_id=%3$d',
                        $tableStore, $attribute->getId(), $this->getStoreId(), $mainTable),
                    array($attributeCode => $valueExpr)
                );
        }
        return $this;
    }

    public function load($parentNode = NULL, $recursionLevel = 0)
    {
        $this->_hideSpecialCategories($this->_select, $this->_table);
        return parent::load($parentNode, $recursionLevel);
    }
}
