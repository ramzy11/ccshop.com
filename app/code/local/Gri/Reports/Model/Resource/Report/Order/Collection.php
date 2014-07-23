<?php

/**
 * Class Gri_Reports_Model_Resource_Report_Order_Collection
 * @method Gri_Reports_Model_Resource_Report_Order getResource()
 */
class Gri_Reports_Model_Resource_Report_Order_Collection extends Gri_Reports_Model_Resource_Report_Collection_Abstract
{
    protected $_periodField = 'order_created_day';

    protected function _applyStoresFilterToSelect(Zend_Db_Select $select)
    {
        $nullCheck = FALSE;
        $storeIds = $this->_storesIds;

        if (!is_array($storeIds)) {
            $storeIds = array($storeIds);
        }

        $storeIds = array_unique($storeIds);

        if ($index = array_search(null, $storeIds)) {
            unset($storeIds[$index]);
            $nullCheck = TRUE;
        }

        $storeIds[0] = ($storeIds[0] == '') ? 0 : $storeIds[0];

        if ($nullCheck) {
            $select->where('o.store_id IN(?) OR o.store_id IS NULL', $storeIds);
        } else {
            $select->where('o.store_id IN(?)', $storeIds);
        }

        return $this;
    }

    public function __construct()
    {
        parent::_construct();
        $this->setModel('adminhtml/report_item');
        $this->_resource = Mage::getResourceModel('gri_reports/report_order');
        $this->setConnection($this->getResource()->getReadConnection());
        $this->_initSelect();
    }

    protected function _initSelect()
    {
        if ($this->_selectInitialized) return $this;
        $select = $this->getSelect();
        $select->from(array('t' => $this->getMainTable()))
            ->joinInner(array('o' => $this->getTable('sales/order')), 'o.entity_id=t.order_id');
        $this->_selectInitialized = TRUE;
        return $this;
    }
}
