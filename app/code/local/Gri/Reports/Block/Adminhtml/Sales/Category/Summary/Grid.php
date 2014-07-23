<?php

/**
 * Class Gri_Reports_Block_Adminhtml_Sales_Category_Summary_Grid
 * @method Gri_Reports_Model_Resource_Report_Order_Item_Collection getCollection
 */
class Gri_Reports_Block_Adminhtml_Sales_Category_Summary_Grid extends Gri_Reports_Block_Adminhtml_Sales_Grid_Abstract
{
    protected $_resourceCollectionName = 'gri_reports/report_order_item_collection';

    protected function _construct()
    {
        parent::_construct();
        $this->setCountTotals(TRUE);
        $this->setCountSubTotals(TRUE);
    }

    protected function _prepareCollection()
    {
        parent::_prepareCollection();
        $filterData = $this->getFilterData();
        $collection = $this->getCollection();
        if ($collection) {
            $groupBy = array(
                $this->getPeriodField(),
                'base_category',
                'sub_category',
                'bottom_category',
            );
            $collection->addExpressionFieldToSelect('sum_qty_sold', 'SUM(oi.qty_ordered - oi.qty_refunded)', array())
                ->addExpressionFieldToSelect('sum_actual_amount', 'SUM(oi.base_row_total - oi.base_discount_amount - oi.base_amount_refunded + IFNULL(oi.base_discount_refunded, 0))', array())
                ->addFieldToFilter('t.base_category_id', array('gt' => 0));
            $collection->getSelect()->group($groupBy);
            $categories = explode(',', $filterData->getCategories());
            $categories = array_unique(array_map('intval', $categories));
            $collection->addCategoriesFilter($categories);
            $totals = Mage::getModel('adminhtml/report_item');
            $totalKeys = array();
            foreach ($this->getColumns() as $column) {
                if ($column->getTotal() == 'sum') $totalKeys[] = $column->getIndex();
            }
            $categoryTotals = array();
            foreach ($collection as $item) {
                $categoryTotals[$item->getData($this->getPeriodField())][$item->getBaseCategory()]['qty'][] = $item->getSumQtySold();
                $categoryTotals[$item->getData($this->getPeriodField())][$item->getBaseCategory()]['amount'][] = $item->getSumActualAmount();
                foreach ($totalKeys as $key) {
                    $totals->setData($key, $totals->getData($key) + $item->getData($key));
                }
            }
            foreach ($categoryTotals as $period => $value) {
                foreach ($value as $category => $v) {
                    $categoryTotals[$period][$category]['qty'] = array_sum($v['qty']);
                    $categoryTotals[$period][$category]['amount'] = array_sum($v['amount']);
                }
            }
            $this->setTotals($totals);
            foreach ($collection as $item) {
                $totalQty = $categoryTotals[$item->getData($this->getPeriodField())][$item->getBaseCategory()]['qty'];
                $totalAmount = $categoryTotals[$item->getData($this->getPeriodField())][$item->getBaseCategory()]['amount'];
                $item->setPercentageQtySold($totalQty ? $item->getSumQtySold() / $totalQty : 0);
                $item->setPercentageActualAmount($totalAmount ? $item->getSumActualAmount() / $totalAmount : 0);
                $averagePrice = $item->getSumQtySold() * 1 ? $item->getSumActualAmount() / $item->getSumQtySold() : 0;
                $item->setAveragePrice(sprintf('%.4f', $averagePrice));
            }
        } else {
            $this->setCollection(Mage::getModel('reports/grouped_collection'));
        }
        return $this;
    }

    protected function _prepareColumns()
    {
        $this->addColumn($this->getPeriodField(), array(
            'header' => Mage::helper('sales')->__('Date'),
            'index' => $this->getPeriodField(),
            'width' => 100,
            'sortable' => FALSE,
            'period_type' => $this->getPeriodType(),
            'renderer' => 'adminhtml/report_sales_grid_column_renderer_date',
            'totals_label' => Mage::helper('sales')->__('Total'),
            'subtotals_label' => Mage::helper('sales')->__('Subtotal'),
            'html_decorators' => array('nobr'),
        ));

        $this->addColumn('base_category', array(
            'header' => $this->__('Base Category'),
            'index' => 'base_category',
            'sortable' => FALSE,
        ));

        $this->addColumn('sub_category', array(
            'header' => $this->__('Sub-Category'),
            'index' => 'sub_category',
            'sortable' => FALSE,
        ));

        $this->addColumn('bottom_category', array(
            'header' => $this->__('Bottom-Category'),
            'index' => 'bottom_category',
            'sortable' => FALSE,
        ));

        $this->addColumn('sum_qty_sold', array(
            'header' => $this->__('Qty Actually Sold Category'),
            'index' => 'sum_qty_sold',
            'sortable' => FALSE,
            'type' => 'number',
            'total' => 'sum',
        ));

        $percentageRenderer = 'gri_reports/adminhtml_sales_grid_column_renderer_percentage';
        $this->addColumn('percentage_qty_sold', array(
            'header' => $this->__('Qty Ratio Category'),
            'index' => 'percentage_qty_sold',
            'sortable' => FALSE,
            'type' => 'percentage',
            'renderer' => $percentageRenderer,
        ));

        $currency = (string)Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE);

        $this->addColumn('sum_actual_amount', array(
            'header' => $this->__('Category Actual Amount'),
            'index' => 'sum_actual_amount',
            'sortable' => FALSE,
            'type' => 'currency',
            'currency_code' => $currency,
            'total' => 'sum',
        ));

        $this->addColumn('percentage_actual_amount', array(
            'header' => $this->__('Amount %'),
            'index' => 'percentage_actual_amount',
            'sortable' => FALSE,
            'type' => 'percentage',
            'renderer' => $percentageRenderer,
        ));

        $this->addColumn('average_price', array(
            'header' => $this->__('Average Price'),
            'index' => 'average_price',
            'sortable' => FALSE,
            'type' => 'currency',
            'currency_code' => $currency,
        ));

        $this->addExportType('*/*/exportCategorySummaryExcel', Mage::helper('adminhtml')->__('Excel XML'));
        $this->addExportType('*/*/exportCategorySummaryCsv', Mage::helper('adminhtml')->__('CSV'));

        return parent::_prepareColumns();
    }
}
