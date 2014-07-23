<?php

class Gri_Cms_Model_Resource_Block extends Mage_Cms_Model_Resource_Block
{

    protected function _getLoadSelect($field, $value, $object)
    {
        if ($field == 'block_id' && (int)$value && Mage::getDesign()->getArea() != 'adminhtml' && $object->getStoreId()) {
            $storeSelect = $this->_getReadAdapter()->select()
                ->from(array('t' => $blockTable = $this->getTable('cms/block')), array())
                ->join(array('s' => $blockTable), 's.identifier = t.identifier', 'block_id')
                ->join(array('bs' => $this->getTable('cms/block_store')), 'bs.block_id = s.block_id', array())
                ->where('t.block_id = ?', $value)
                ->where('bs.store_id IN (?)', array(0, $object->getStoreId()));
            ($storeValue = $storeSelect->query()->fetchColumn()) and $value = $storeValue;
        }

        $select = parent::_getLoadSelect($field, $value, $object);
        if ($object->getLoadInactive()) {
            $where = $select->getPart($select::WHERE);
            $select->reset($select::WHERE);
            $or = $select::SQL_OR;
            $and = $select::SQL_AND;
            $lengthOr = strlen($or);
            $lengthAnd = strlen($and);
            foreach ($where as $v) {
                if (strpos($v, 'is_active = ') !== FALSE) continue;
                $v = substr($v, 0, -1);
                substr($v, 0, $lengthAnd) == $and and $v = substr($v, $lengthAnd + 1);
                substr($v, 0, $lengthOr) == $or ?
                    $select->orWhere(substr($v, $lengthOr + 2)) :
                    $select->where(substr($v, 1));
            }
        }
        return $select;
    }

    /**
     * Check if page identifier exist for specific store
     * return page id if page exists
     *
     * @param string $identifier
     * @param int $storeId
     * @return int
     */
    public function checkIdentifier($identifier, $storeId)
    {
        $stores = array(Mage_Core_Model_App::ADMIN_STORE_ID, $storeId);
        $select = $this->_getLoadByIdentifierSelect($identifier, $stores, 1);
        $select->reset(Zend_Db_Select::COLUMNS)
            ->columns('cb.block_id')
            ->order('cbs.store_id DESC')
            ->limit(1);

        return $this->_getReadAdapter()->fetchOne($select);
    }

    /**
     * Retrieve load select with filter by identifier, store and activity
     *
     * @param string $identifier
     * @param int|array $store
     * @param int $isActive
     * @return Varien_Db_Select
     */
    protected function _getLoadByIdentifierSelect($identifier, $store, $isActive = null)
    {
        $select = $this->_getReadAdapter()->select()
            ->from(array('cb' => $this->getMainTable()))
            ->join(
                array('cbs' => $this->getTable('cms/block_store')),
                'cb.block_id = cbs.block_id',
                array())
            ->where('cb.identifier = ?', $identifier)
            ->where('cbs.store_id IN (?)', $store);

        if (!is_null($isActive)) {
            $select->where('cb.is_active = ?', $isActive);
        }

        return $select;
    }

}
