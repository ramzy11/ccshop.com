<?php

/**
 * Class Gri_Reports_Model_Resource_Report_Coupon_Collection
 * @method Gri_Reports_Model_Resource_Report_Coupon getResource()
 */
class Gri_Reports_Model_Resource_Report_Coupon_Collection extends Gri_Reports_Model_Resource_Report_Collection_Abstract
{
    protected $_periodField = NULL;

    public function __construct()
    {
        parent::_construct();
        $this->setModel('adminhtml/report_item');
        $this->_resource = Mage::getResourceModel('gri_reports/report_coupon');
        $this->setConnection($this->getResource()->getReadConnection());
        $this->_initSelect();
    }

    protected function _applyDateRangeFilter()
    {
        if ($this->_from !== NULL) {
            $this->getSelect()->where('from_date >= ? OR from_date IS NULL', $this->_from);
        }
        if ($this->_to !== NULL) {
            $this->getSelect()->where('to_date <= ? OR to_date IS NULL', $this->_to);
        }
        return $this;
    }

    protected function _applyStoresFilter()
    {
        return $this;
    }

    protected function _initSelect()
    {
        if ($this->_selectInitialized) return $this;
        $select = $this->getSelect();
        $select->from(array('t' => $this->getMainTable()))
            ->joinInner(array('r' => $this->getTable('salesrule/rule')), 'r.rule_id=t.rule_id');
        $this->_selectInitialized = TRUE;
        return $this;
    }
}
