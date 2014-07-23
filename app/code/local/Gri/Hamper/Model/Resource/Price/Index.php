<?php

class Gri_Hamper_Model_Resource_Price_Index extends Mage_Bundle_Model_Resource_Price_Index
{

    public function getProducts($product = null, $lastEntityId = 0, $limit = 100)
    {
        $select = $this->_getReadAdapter()->select()
            ->from(
                array('e' => $this->getTable('catalog/product')),
                array('entity_id')
            )
            ->where('e.type_id=?', Gri_Hamper_Model_Product_Type::TYPE_HAMPER);
        if ($product instanceof Mage_Catalog_Model_Product) {
            $select->where('e.entity_id=?', $product->getId());
        } elseif ($product instanceof Mage_Catalog_Model_Product_Condition_Interface) {
            $value = new Zend_Db_Expr($product->getIdsSelect($this->_getReadAdapter()));
            $select->where('e.entity_id IN(?)', $value);
        } elseif (is_numeric($product) || is_array($product)) {
            $select->where('e.entity_id IN(?)', $product);
        }

        $priceType = $this->_getAttribute('price_type');
        $priceTypeAlias = 't_' . $priceType->getAttributeCode();
        $joinConds = array(
            $priceTypeAlias . '.attribute_id=:attribute_id',
            $priceTypeAlias . '.store_id=0',
            $priceTypeAlias . '.entity_id=e.entity_id'
        );

        $select->joinLeft(
            array($priceTypeAlias => $priceType->getBackend()->getTable()),
            implode(' AND ', $joinConds),
            array('price_type' => $priceTypeAlias . '.value')
        );

        $select->where('e.entity_id>:last_entity_id', $lastEntityId)
            ->order('e.entity_id')
            ->limit($limit);
        $bind = array(
            'attribute_id'   => $priceType->getAttributeId(),
            'last_entity_id' => $lastEntityId
        );
        return $this->_getReadAdapter()->fetchPairs($select, $bind);
    }
}
