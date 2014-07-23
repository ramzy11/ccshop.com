<?php

class Gri_Hamper_Model_Resource_Selection extends Mage_Bundle_Model_Resource_Selection
{
    protected $_useRaw = FALSE;
    protected $_selectionFilter;

    public function getChildrenIds($parentId, $required = TRUE)
    {
        $childrenIds = array();
        $notRequired = array();
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from(
                array('tbl_selection' => $this->getMainTable()),
                array('product_id', 'parent_product_id', 'option_id', 'selection_id')
            )
            ->join(
                array('e' => $this->getTable('catalog/product')),
                'e.entity_id = tbl_selection.product_id',
                array()
            )
            ->join(
                array('tbl_option' => $this->getTable('bundle/option')),
                'tbl_option.option_id = tbl_selection.option_id',
                array('required')
            )
            ->where('tbl_selection.parent_product_id = :parent_id')
            ->order(array('tbl_option.position ASC', 'tbl_selection.position ASC', 'e.entity_id ASC'));
        $this->getSelectionFilter() and $select->where(
            $adapter->quoteInto('tbl_selection.selection_id IN (?)', (array)$this->getSelectionFilter())
        );
        $data = $adapter->fetchAll($select, array('parent_id' => $parentId));
        foreach ($data as $k => $row) {
            if ($row['required']) {
                $childrenIds[$row['option_id']][$row['selection_id']] = $row['product_id'];
            } else {
                $notRequired[$row['option_id']][$row['selection_id']] = $row['product_id'];
            }
            $data[$k] = $row['product_id'];
        }
        if ($this->getUseRaw()) return $data;

        if (!$required) {
            $childrenIds += $notRequired;
        } else {
            if (!$childrenIds) {
                foreach ($notRequired as $groupedChildrenIds) {
                    foreach ($groupedChildrenIds as $childId) {
                        $childrenIds[0][$childId] = $childId;
                    }
                }
            }
            if (!$childrenIds) {
                $childrenIds = array(array());
            }
        }

        return $childrenIds;
    }

    /**
     * @return array
     */
    public function getSelectionFilter()
    {
        return $this->_selectionFilter;
    }

    /**
     * @return boolean
     */
    public function getUseRaw()
    {
        return $this->_useRaw;
    }

    /**
     * @param array $selectionFilter
     * @return $this
     */
    public function setSelectionFilter($selectionFilter)
    {
        $this->_selectionFilter = $selectionFilter;
        return $this;
    }

    /**
     * @param boolean $useRaw
     * @return $this
     */
    public function setUseRaw($useRaw)
    {
        $this->_useRaw = $useRaw;
        return $this;
    }
}
