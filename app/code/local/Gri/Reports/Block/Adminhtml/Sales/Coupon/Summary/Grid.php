<?php

/**
 * Class Gri_Reports_Block_Adminhtml_Sales_Coupon_Summary_Grid
 * @method Gri_Reports_Model_Resource_Report_Coupon_Collection getCollection
 */
class Gri_Reports_Block_Adminhtml_Sales_Coupon_Summary_Grid extends Gri_Reports_Block_Adminhtml_Sales_Grid_Abstract
{
    protected $_columnGroupBy = 'rule_id';
    protected $_resourceCollectionName = 'gri_reports/report_coupon_collection';

    protected function _prepareCollection()
    {
        parent::_prepareCollection();
        $filterData = $this->getFilterData();
        $collection = $this->getCollection();
        if (!$collection && ($this->getRequest()->getParam('filter') !== NULL ||
            strtolower(substr($this->getRequest()->getActionName(), 0, 6)) == 'export'
        )) {
            $this->setCollection($collection = Mage::getResourceModel($this->getResourceCollectionName())
                ->setDateRange($filterData->getData('from', null), $filterData->getData('to', null)));
        }
        if ($collection) {
            $this->setCountTotals(TRUE);
            $this->setCountSubTotals(TRUE);
            $collection->setColumnGroupBy($this->_columnGroupBy);
            if ($ruleId = $filterData->getRuleId()) {
                $collection->addFieldToFilter('t.rule_id', $ruleId);
            }
            if ($ruleName = $filterData->getRuleName()) {
                $collection->addFieldToFilter('r.name', $ruleName);
            }
            if ($couponCode = $filterData->getCouponCode()) {
                $collection->addFieldToFilter('t.code', $couponCode);
            }
            $collection->addExpressionFieldToSelect('coupon_count', 'COUNT(t.coupon_id)', array());
            $collection->getSelect()->group('t.rule_id');
            $totals = Mage::getModel('adminhtml/report_item');
            $totalKeys = array();
            foreach ($this->getColumns() as $column) {
                if ($column->getTotal() == 'sum') $totalKeys[] = $column->getIndex();
            }
            foreach ($collection as $item) {
                foreach ($totalKeys as $key) {
                    $totals->setData($key, $totals->getData($key) + $item->getData($key));
                }
            }
            $this->setTotals($totals);
            $this->getIsExport() or $collection->groupData();
        } else {
            $this->setCollection(Mage::getModel('reports/grouped_collection'));
        }
        return $this;
    }

    protected function _prepareColumns()
    {
        $this->addColumn('rule_id', array(
            'header' => $this->__('Price Rule ID'),
            'index' => 'rule_id',
            'width' => 100,
            'sortable' => FALSE,
            'type' => 'number',
            'totals_label' => Mage::helper('sales')->__('Total'),
            'subtotals_label' => Mage::helper('sales')->__('Subtotal'),
            'html_decorators' => array('nobr'),
        ));

        $this->addColumn('name', array(
            'header' => $this->__('Price Rule Name'),
            'index' => 'name',
            'sortable' => FALSE,
        ));

        $this->addColumn('from_date', array(
            'header' => $this->__('From Date'),
            'index' => 'from_date',
            'sortable' => FALSE,
            'type' => 'date',
        ));

        $this->addColumn('to_date', array(
            'header' => $this->__('To Date'),
            'index' => 'to_date',
            'sortable' => FALSE,
            'type' => 'date',
        ));

        $this->addColumn('uses_per_coupon', array(
            'header' => $this->__('Uses per Coupon'),
            'index' => 'uses_per_coupon',
            'sortable' => FALSE,
            'type' => 'number',
        ));

        $this->addColumn('uses_per_customer', array(
            'header' => $this->__('Uses per Customer'),
            'index' => 'uses_per_customer',
            'sortable' => FALSE,
            'type' => 'number',
        ));

        $this->addColumn('coupon_count', array(
            'header' => $this->__('Coupon Count'),
            'index' => 'coupon_count',
            'sortable' => FALSE,
            'type' => 'number',
            'total' => 'sum',
        ));

        $this->addColumn('times_used', array(
            'header' => $this->__('Coupon Usage Count'),
            'index' => 'times_used',
            'sortable' => FALSE,
            'type' => 'number',
            'total' => 'sum',
        ));

        $this->addExportType('*/*/exportCouponSummaryExcel', Mage::helper('adminhtml')->__('Excel XML'));
        $this->addExportType('*/*/exportCouponSummaryCsv', Mage::helper('adminhtml')->__('CSV'));

        return parent::_prepareColumns();
    }
}
