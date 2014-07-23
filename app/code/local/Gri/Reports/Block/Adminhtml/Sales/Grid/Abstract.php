<?php

/**
 * Class Gri_Reports_Block_Adminhtml_Sales_Grid_Abstract
 * @method Varien_Object getFilterData()
 */
class Gri_Reports_Block_Adminhtml_Sales_Grid_Abstract extends Mage_Adminhtml_Block_Report_Grid_Abstract
{
    protected $_exportPageSize = 60000;
    protected $_columnGroupBy;
    protected $_groupByColumns = array(
        'order_created_day' => array(
            'day' => 'order_created_day',
            'month' => 'order_created_month',
            'year' => 'order_created_year',
        ),
        'order_shipping_day' => array(
            'day' => 'order_shipping_day',
            'month' => 'order_shipping_month',
            'year' => 'order_shipping_year',
        ),
    );
    protected $_isExport = TRUE;
    protected $_isGriExport = FALSE;
    protected $_periodField = 'order_created_day';
    protected $_resourceCollectionName = 'gri_reports/report_order_collection';

    protected function _prepareCollection()
    {
        parent::_prepareCollection();
        if ($this->getCollection()) {
            $this->getCollection()->setColumnGroupBy($this->_columnGroupBy = $this->getPeriodField())
                ->setPeriodField($this->_columnGroupBy);
            $this->isColumnGrouped($this->_columnGroupBy, TRUE);
        }
    }

    protected function _prepareGrid()
    {
        $filterData = $this->getFilterData();
        isset($this->_groupByColumns[$filterData->getReportType()][$filterData->getPeriodType()]) and
            $this->setPeriodField($this->_groupByColumns[$filterData->getReportType()][$filterData->getPeriodType()]);
        parent::_prepareGrid();
        $collection = $this->getCollection();
        if (!$this->_isGriExport &&
            $collection instanceof Gri_Reports_Model_Resource_Report_Collection_Abstract &&
            $collection->getColumnGroupBy()
        ) $collection->groupData();
        return $this;
    }

    public function addColumn($columnId, $column)
    {
        if (isset($column['type']) && $column['type'] == 'currency') {
            if ($this->_isGriExport) {
                $column['type'] = 'number';
            } else {
                $column['renderer'] = 'gri_reports/adminhtml_sales_grid_column_renderer_currency';
            }
        }
        return parent::addColumn($columnId, $column);
    }

    /**
     * @return Gri_Reports_Model_Resource_Report_Order_Collection
     */
    public function getCollection()
    {
        return $this->_collection;
    }

    public function getIsExport()
    {
        return $this->_isGriExport;
    }

    public function getPeriodField()
    {
        return $this->_periodField;
    }

    /**
     * Remove Group by Columns for Export Mode
     * @param bool $isExport
     * @return Gri_Reports_Block_Adminhtml_Sales_Grid_Abstract
     */
    public function setIsExport($isExport = TRUE)
    {
        $this->_isGriExport = $isExport;
        return $this;
    }

    public function setPeriodField($periodField)
    {
        $this->_periodField = $periodField;
        return $this;
    }
}
