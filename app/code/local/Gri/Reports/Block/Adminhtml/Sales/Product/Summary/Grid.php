<?php

/**
 * Class Gri_Reports_Block_Adminhtml_Sales_Category_Summary_Grid
 * @method Gri_Reports_Model_Resource_Report_Order_Item_Collection getCollection
 */
class Gri_Reports_Block_Adminhtml_Sales_Product_Summary_Grid extends Gri_Reports_Block_Adminhtml_Sales_Grid_Abstract
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
                $periodField = $this->getPeriodField(),
                'style_no',
                'color',
            );

            $categories = explode(',', $filterData->getCategories());
            $categories = array_unique(array_map('intval', $categories));
            $collection->addCategoriesFilter($categories);
            $collection->addExpressionFieldToSelect('sum_qty_sold', 'SUM(oi.qty_ordered - oi.qty_refunded)', array())
                ->addExpressionFieldToSelect('sum_actual_amount', 'SUM(oi.base_row_total - oi.base_discount_amount - oi.base_amount_refunded + IFNULL(oi.base_discount_refunded, 0))', array());
            if ($styleNo = $filterData->getStyleNo()) {
                $collection->addFieldToFilter('t.style_no', $styleNo);
            }
            if ($styleName = $filterData->getStyleName()) {
                $collection->addFieldToFilter('t.style_name', $styleName);
            }

            $collection->getSelect()->group($groupBy);
            $collection->setOrder($periodField, $collection::SORT_ORDER_ASC);
            $collection->setOrder('sum_actual_amount');
            $collection->setOrder('style_no', $collection::SORT_ORDER_ASC);
            $collection->setOrder('color', $collection::SORT_ORDER_ASC);

            $totals = Mage::getModel('adminhtml/report_item');
            $totalKeys = array();
            foreach ($this->getColumns() as $column) {
                if ($column->getTotal() == 'sum')
                     $totalKeys[] = $column->getIndex();
            }
            foreach ($collection as $item) {
                foreach ($totalKeys as $key) {
                    $totals->setData($key, $totals->getData($key) + $item->getData($key));
                }
            }

            // total
            $totalQty = $totals->getSumQtySold();
            $totalAmount = $totals->getSumActualAmount();

            $this->setTotals($totals);
            foreach ($collection as $item) {
               $item->setPercentageQtySold($totalQty ? $item->getSumQtySold() / $totalQty : 0);
                $item->setPercentageActualAmount($totalAmount ? $item->getSumActualAmount() / $totalAmount : 0);
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

        $this->addColumn('brand', array(
            'header' => $this->__('Brand'),
            'index' => 'brand',
            'sortable' => FALSE,
        ));

        $this->addColumn('style_no', array(
            'header' => $this->__('Style Number'),
            'index' => 'style_no',
            'sortable' => FALSE,
        ));

        $this->addColumn('style_name', array(
            'header' => $this->__('Style Name'),
            'index' => 'style_name',
            'sortable' => FALSE,
        ));

        $this->addColumn('color', array(
            'header' => $this->__('Color'),
            'index' => 'color',
            'sortable' => FALSE,
        ));

        $this->addColumn('sum_qty_sold', array(
            'header' => $this->__('Product Qty Actually Sold'),
            'index' => 'sum_qty_sold',
            'sortable' => FALSE,
            'type' => 'number',
            'total' => 'sum',
        ));

        $currency = (string)Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE);
        $this->addColumn('sum_actual_amount', array(
            'header' => $this->__('Product Actual Amount'),
            'index' => 'sum_actual_amount',
            'sortable' => FALSE,
            'type' => 'currency',
            'currency_code' => $currency,
            'total' => 'sum',
        ));

        $percentageRenderer = 'gri_reports/adminhtml_sales_grid_column_renderer_percentage';
        $this->addColumn('percentage_qty_sold', array(
            'header' => $this->__('Qty Ratio'),
            'index' => 'percentage_qty_sold',
            'sortable' => FALSE,
            'type' => 'percentage',
            'renderer' => $percentageRenderer,
        ));

        $this->addColumn('percentage_actual_amount', array(
            'header' => $this->__('Amount %'),
            'index' => 'percentage_actual_amount',
            'sortable' => FALSE,
            'type' => 'percentage',
            'renderer' => $percentageRenderer,
        ));

        $this->addColumn('product_created_at', array(
            'header' => $this->__('Product Created At'),
            'index' => 'product_created_at',
            'sortable' => FALSE,
            'type' => 'datetime',
        ));

        $this->addExportType('*/*/exportProductSummaryExcel', Mage::helper('adminhtml')->__('Excel XML'));
        $this->addExportType('*/*/exportProductSummaryCsv', Mage::helper('adminhtml')->__('CSV'));

        return parent::_prepareColumns();
    }
}
