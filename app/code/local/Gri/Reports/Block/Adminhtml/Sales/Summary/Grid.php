<?php

/**
 * Class Gri_Reports_Block_Adminhtml_Sales_Summary_Grid
 * @method Gri_Reports_Model_Resource_Report_Order_Item_Collection getCollection
 */
class Gri_Reports_Block_Adminhtml_Sales_Summary_Grid extends Gri_Reports_Block_Adminhtml_Sales_Grid_Abstract
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
        $collection = $this->getCollection();
        if ($collection) {
            $filterData = $this->getFilterData();
            $presetGroupByFields = array(
                'summary' => array(
                    $this->getPeriodField(),
                ),
                'group_by_category' => array(
                    $this->getPeriodField(),
                    'base_category',
                    'sub_category',
                    'bottom_category',
                ),
                'group_by_style_no' => array(
                    $this->getPeriodField(),
                    'style_no',
                ),
            );
            $outputType = $filterData->getOutputType();
            isset($presetGroupByFields[$outputType]) or $outputType = 'summary';
            $groupBy = $presetGroupByFields[$outputType];
            $collection->addExpressionFieldToSelect('sum_row_total', 'SUM(oi.base_row_total - oi.base_discount_amount)', array())
                ->addExpressionFieldToSelect('order_count', 'COUNT(DISTINCT o.entity_id)', array())
                ->addExpressionFieldToSelect('sum_qty_ordered', 'SUM(oi.qty_ordered)', array())
                ->addExpressionFieldToSelect('sum_qty_refunded', 'SUM(oi.qty_refunded)', array())
                ->addExpressionFieldToSelect('sum_amount_refunded', 'SUM(oi.base_amount_refunded - IFNULL(oi.base_discount_refunded, 0))', array())
                ->addExpressionFieldToSelect('sum_qty_sold', 'SUM(oi.qty_ordered - oi.qty_refunded)', array())
                ->addExpressionFieldToSelect('sum_actual_amount', 'SUM(oi.base_row_total - oi.base_discount_amount - oi.base_amount_refunded + IFNULL(oi.base_discount_refunded, 0))', array())
                ->addExpressionFieldToSelect('category', 'CONCAT(t.base_category, " > ", t.sub_category, " > ", t.bottom_category)', array());
            $collection->getSelect()->group($groupBy);
            is_array($this->getCategories()) and $collection->addCategoriesFilter($this->getCategories());
            $totals = Mage::getModel('adminhtml/report_item');
            $totalKeys = array();
            foreach ($this->getColumns() as $column) {
                if ($column->getTotal() == 'sum') $totalKeys[] = $column->getIndex();
            }
            foreach ($collection as $item) {
                if ($category = $item->getCategory()) {
                    $category = explode($sep = ' > ', $category);
                    foreach ($category as $k => $v) {
                        if (!$v) unset($category[$k]);
                    }
                    $item->setCategory(implode($sep, $category));
                }
                foreach ($totalKeys as $key) {
                    $totals->setData($key, $totals->getData($key) + $item->getData($key));
                }
            }
            $this->setTotals($totals);
        } else {
            $this->setCollection(Mage::getModel('reports/grouped_collection'));
        }
        return $this;
    }

    protected function _prepareColumns()
    {
        $filterData = $this->getFilterData();
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

        switch ($filterData->getOutputType()) {
            case 'group_by_category':
                $categories = explode(',', $filterData->getCategories());
                $categories = array_unique(array_map('intval', $categories));
                $this->setCategories($categories);

                $this->addColumn('category', array(
                    'header' => $this->__('Category'),
                    'index' => 'category',
                    'sortable' => FALSE,
                ));
                break;
            case 'group_by_style_no':
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
                break;
        }

        $this->addColumn('order_count', array(
            'header' => $this->__('No. of Order'),
            'index' => 'order_count',
            'sortable' => FALSE,
            'type' => 'number',
            'total' => 'sum',
        ));

        $this->addColumn('sum_qty_ordered', array(
            'header' => $this->__('Qty Ordered'),
            'index' => 'sum_qty_ordered',
            'sortable' => FALSE,
            'type' => 'number',
            'total' => 'sum',
        ));

        $currency = (string)Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE);
        $this->addColumn('sum_row_total', array(
            'header' => $this->__('Sold Amount'),
            'index' => 'sum_row_total',
            'sortable' => FALSE,
            'type' => 'currency',
            'currency_code' => $currency,
            'total' => 'sum',
        ));

        $this->addColumn('sum_qty_refunded', array(
            'header' => $this->__('Qty Returned'),
            'index' => 'sum_qty_refunded',
            'sortable' => FALSE,
            'type' => 'number',
            'total' => 'sum',
        ));

        $this->addColumn('sum_amount_refunded', array(
            'header' => $this->__('Refunded Amount'),
            'index' => 'sum_amount_refunded',
            'sortable' => FALSE,
            'type' => 'currency',
            'currency_code' => $currency,
            'total' => 'sum',
        ));

        $this->addColumn('sum_qty_sold', array(
            'header' => $this->__('Qty Actually Sold Summary'),
            'index' => 'sum_qty_sold',
            'sortable' => FALSE,
            'type' => 'number',
            'total' => 'sum',
        ));

        $this->addColumn('sum_actual_amount', array(
            'header' => $this->__('Actual Amount'),
            'index' => 'sum_actual_amount',
            'sortable' => FALSE,
            'type' => 'currency',
            'currency_code' => $currency,
            'total' => 'sum',
        ));

        $this->addExportType('*/*/exportSummaryExcel', Mage::helper('adminhtml')->__('Excel XML'));
        $this->addExportType('*/*/exportSummaryCsv', Mage::helper('adminhtml')->__('CSV'));

        return parent::_prepareColumns();
    }
}
