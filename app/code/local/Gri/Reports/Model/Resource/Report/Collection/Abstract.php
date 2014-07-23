<?php

/**
 * Class Gri_Reports_Model_Resource_Report_Collection_Abstract
 * @method Gri_Reports_Model_Resource_Report_Abstract getResource()
 * @method Gri_Reports_Model_Resource_Report_Collection_Abstract join($table, $cond, $cols = '*')
 */
class Gri_Reports_Model_Resource_Report_Collection_Abstract extends Mage_Reports_Model_Resource_Report_Collection_Abstract
{
    protected $_categories = array();
    protected $_columnGroupBy;
    protected $_orderStatus;
    protected $_periodField = 'period';
    protected $_selectInitialized;

    protected function _applyDateRangeFilter()
    {
        $dayField = $this->_periodField;
        $dayField = str_replace(array('month', 'year'), 'day', $dayField);
        // Remember that field PERIOD is a DATE(YYYY-MM-DD) in all databases including Oracle
        if ($this->_from !== NULL) {
            $this->getSelect()->where($dayField . ' >= ?', $this->_from);
        }
        if ($this->_to !== NULL) {
            $this->getSelect()->where($dayField . ' <= ?', $this->_to . ' 23:59:59');
        }
        return $this;
    }

    protected function _applyCategoryFilter()
    {
        if (!$this->_categories) {
            return $this;
        }
        $this->getSelect()->where('base_category_id IN(?) OR sub_category_id IN(?) OR bottom_category_id IN(?)', array($this->_categories));
        return $this;
    }

    protected function _applyCustomFilter()
    {
        $this->_applyCategoryFilter()
            ->_applyOrderStatusFilter();
        return $this;
    }

    /**
     * Apply order status filter
     *
     * @return Mage_Sales_Model_Resource_Report_Collection_Abstract
     */
    protected function _applyOrderStatusFilter()
    {
        if (is_null($this->_orderStatus)) {
            return $this;
        }
        $orderStatus = $this->_orderStatus;
        if (!is_array($orderStatus)) {
            $orderStatus = array($orderStatus);
        }
        $this->getSelect()->where('o.status IN(?)', $orderStatus);
        return $this;
    }

    public function addCategoriesFilter(array $categories)
    {
        foreach ($categories as $k => $v) {
            if (!$v) unset($categories[$k]);
        }
        $this->_categories = $categories;
        return $this;
    }

    public function addOrderStatusFilter($orderStatus)
    {
        $this->_orderStatus = $orderStatus;
        return $this;
    }

    public function getColumnGroupBy()
    {
        return $this->_columnGroupBy;
    }

    public function groupData()
    {
        if (count($this->_items) == 0) {
            return $this;
        }
        $groupedItems = array();
        foreach ($this->_items as $item) {
            if (isset($groupedItems[$key = $item->getData($this->_columnGroupBy)])) {
                $groupedItems[$key]->addChild($item->setIsEmpty(FALSE));
            } else {
                $groupedItems[$key] = $item->setIsEmpty(FALSE);
            }
        }
        $this->_items = $groupedItems;

        return $this;
    }

    public function joinLeft($table, $cond, $cols = '*')
    {
        if (is_array($table)) {
            foreach ($table as $k => $v) {
                $alias = $k;
                $table = $v;
                break;
            }
        } else {
            $alias = $table;
        }

        if (!isset($this->_joinedTables[$table])) {
            $this->getSelect()->joinLeft(array($alias => $this->getTable($table)), $cond, $cols);
            $this->_joinedTables[$alias] = TRUE;
        }
        return $this;
    }

    public function setColumnGroupBy($column)
    {
        $this->_columnGroupBy = (string)$column;
        return $this;
    }

    public function setIdFieldName($name)
    {
        $this->_idFieldName = $name;
        $this->getResource()->setIdFieldName($name);
        return $this;
    }

    public function setIsLoaded($loaded = TRUE)
    {
        $this->_isCollectionLoaded = $loaded;
    }

    public function setPeriodField($periodField)
    {
        $this->_periodField = $periodField;
        return $this;
    }

    public function setSelect(Zend_Db_Select $select)
    {
        $this->_select = $select;
        return $this;
    }
}
