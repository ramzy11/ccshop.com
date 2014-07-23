<?php

/**
 * Class Gri_Reports_Block_Adminhtml_Sales_Discount_Summary_Grid
 * @method Gri_Reports_Model_Resource_Report_Order_Item_Collection getCollection
 */
class Gri_Reports_Block_Adminhtml_Sales_Discount_Summary_Grid extends Gri_Reports_Block_Adminhtml_Sales_Grid_Abstract
{
    protected $_discountRanges = array(
        '100' => '100%',
        '085' => '85% - 99%',
        '070' => '70% - 84%',
        '050' => '50% - 69%',
        '030' => '30% - 49%',
        '000' => '<30%',
    );
    protected $_resourceCollectionName = 'gri_reports/report_order_item_collection';

    protected function _construct()
    {
        parent::_construct();
        $this->setCountTotals(TRUE);
        $this->setCountSubTotals(TRUE);
    }

    protected function _getDiscountRange($discount)
    {
        foreach ($this->_discountRanges as $k => $v) {
            if ($discount >= $k / 100) return $k;
        }
        return '';
    }

    protected function _prepareCollection()
    {
        parent::_prepareCollection();
        $collection = $this->getCollection();
        if ($collection) {
            $collection->addExpressionFieldToSelect('qty_sold', '(oi.qty_ordered - oi.qty_refunded)', array())
                ->addExpressionFieldToSelect('actual_amount', '(oi.base_row_total - oi.base_discount_amount - oi.base_amount_refunded + IFNULL(oi.base_discount_refunded, 0))', array())
                ->addExpressionFieldToSelect('row_discount_amount', 'oi.base_discount_amount', array());
            $totals = Mage::getModel('adminhtml/report_item');
            $totalKeys = array();
            foreach ($this->getColumns() as $column) {
                if ($column->getTotal() == 'sum') $totalKeys[] = $column->getIndex();
            }
            $data = array();
            $periodFiled = $this->getPeriodField();
            foreach ($collection as $item) {
                $discount = $item->getBaseRowTotal() * 1 ? 1 - $item->getRowDiscountAmount() / $item->getBaseRowTotal() : 1;
                $data[$item->getData($periodFiled)][$this->_getDiscountRange($discount)][] = $item;
                foreach ($totalKeys as $key) {
                    $totals->setData($key, $totals->getData($key) + $item->getData($key));
                }
            }
            $this->setTotals($totals);
            $collection->clear();
            $collection->setIsLoaded();
            foreach ($data as $period => $v) {
                krsort($v);
                foreach ($v as $range => $items) {
                    isset($this->_discountRanges[$range]) and $range = $this->_discountRanges[$range];
                    $item = Mage::getModel($collection->getModelName());
                    $item->setData($periodFiled, $period)
                        ->setDiscountRange($range);
                    foreach ($items as $i) {
                        $item->setQtySold($item->getQtySold() + $i->getQtySold())
                            ->setActualAmount($item->getActualAmount() + $i->getActualAmount());
                    }
                    $item->setPercentageQtySold($totals->getQtySold() ? $item->getQtySold() / $totals->getQtySold() : 0);
                    $item->setPercentageActualAmount($totals->getActualAmount() ? $item->getActualAmount() / $totals->getActualAmount() : 0);
                    $collection->addItem($item);
                }
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

        $this->addColumn('discount_range', array(
            'header' => $this->__('Discount Range'),
            'index' => 'discount_range',
            'sortable' => FALSE,
        ));

        $this->addColumn('qty_sold', array(
            'header' => $this->__('Qty'),
            'index' => 'qty_sold',
            'sortable' => FALSE,
            'type' => 'number',
            'total' => 'sum',
        ));

        $currency = (string)Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE);

        $this->addColumn('actual_amount', array(
            'header' => $this->__('Discount Actual Amount'),
            'index' => 'actual_amount',
            'sortable' => FALSE,
            'type' => 'currency',
            'currency_code' => $currency,
            'total' => 'sum',
        ));

        $percentageRenderer = 'gri_reports/adminhtml_sales_grid_column_renderer_percentage';
        $this->addColumn('percentage_qty_sold', array(
            'header' => $this->__('Sales Qty Ratio'),
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

        $this->addExportType('*/*/exportDiscountSummaryExcel', Mage::helper('adminhtml')->__('Excel XML'));
        $this->addExportType('*/*/exportDiscountSummaryCsv', Mage::helper('adminhtml')->__('CSV'));

        return parent::_prepareColumns();
    }
}
