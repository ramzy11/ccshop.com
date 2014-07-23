<?php

/**
 * Class Gri_Reports_Block_Adminhtml_Sales_Order_Grid
 * @method Gri_Reports_Model_Resource_Report_Order_Item_Collection getCollection()
 */
class Gri_Reports_Block_Adminhtml_Sales_Order_Detail_Grid extends Gri_Reports_Block_Adminhtml_Sales_Grid_Abstract
{
    protected $_resourceCollectionName = 'gri_reports/report_order_item_collection';

    protected function _construct()
    {
        parent::_construct();
        $this->setCountTotals(TRUE);
        $this->setCountSubTotals(TRUE);
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

        $this->addColumn('increment_id', array(
            'header' => $this->__('Detail Order #'),
            'index' => 'increment_id',
            'sortable' => FALSE,
        ));

        $this->addColumn('customer_email', array(
            'header' => $this->__('Customer Email'),
            'index' => 'customer_email',
            'sortable' => FALSE,
        ));

        $this->addColumn('name', array(
            'header' => $this->__('Product Name'),
            'index' => 'name',
            'sortable' => FALSE,
        ));

        $this->addColumn('sku', array(
            'header' => $this->__('SKU'),
            'index' => 'sku',
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

        $this->addColumn('size', array(
            'header' => $this->__('Size'),
            'index' => 'size',
            'sortable' => FALSE,
        ));

        $this->addColumn('qty_ordered', array(
            'header' => $this->__('Qty Ordered Detail'),
            'index' => 'qty_ordered',
            'type' => 'number',
            'total' => 'sum',
            'sortable' => FALSE,
        ));

        $currency = (string)Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE);
        $this->addColumn('list_price', array(
            'header' => $this->__('Price'),
            'index' => 'list_price',
            'type' => 'currency',
            'currency_code' => $currency,
            'sortable' => FALSE,
        ));

        $this->addColumn('price', array(
            'header' => $this->__('Sale Price'),
            'index' => 'base_price',
            'type' => 'currency',
            'currency_code' => $currency,
            'sortable' => FALSE,
        ));

        $this->addColumn('row_discount_amount', array(
            'header' => $this->__('Discount Amount'),
            'index' => 'row_discount_amount',
            'type' => 'currency',
            'currency_code' => $currency,
            'total' => 'sum',
            'sortable' => FALSE,
        ));

        $percentageRenderer = 'gri_reports/adminhtml_sales_grid_column_renderer_percentage';
        $this->addColumn('discount_rate', array(
            'header' => $this->__('Discount Rate'),
            'index' => 'discount_rate',
            'sortable' => FALSE,
            'type' => 'percentage',
            'renderer' => $percentageRenderer,
        ));

        $this->addColumn('sold_amount', array(
            'header' => $this->__('Row Total'),
            'index' => 'sold_amount',
            'sortable' => FALSE,
            'type' => 'currency',
            'currency_code' => $currency,
            'total' => 'sum',
        ));

        $this->addColumn('brand', array(
            'header' => $this->__('Brand'),
            'index' => 'brand',
            'sortable' => FALSE,
        ));

        $this->addColumn('base_category', array(
            'header' => $this->__('Base Category'),
            'index' => 'base_category',
            'sortable' => FALSE,
        ));

        $this->addColumn('sub_category', array(
            'header' => $this->__('Sub Category'),
            'index' => 'base_category',
            'sortable' => FALSE,
        ));

        $this->addColumn('bottom_category', array(
            'header' => $this->__('Bottom Category'),
            'index' => 'bottom_category',
            'sortable' => FALSE,
        ));

        $this->addExportType('*/*/exportOrderDetailsExcel', Mage::helper('adminhtml')->__('Excel XML'));
        $this->addExportType('*/*/exportOrderDetailsCsv', Mage::helper('adminhtml')->__('CSV'));

        return parent::_prepareColumns();
    }

    protected function _prepareCollection()
    {
        parent::_prepareCollection();
        $filterData = $this->getFilterData();
        $collection = $this->getCollection();
        if ($collection) {
            $collection->addExpressionFieldToSelect('row_discount_amount', 'oi.base_discount_amount', array())
                ->addExpressionFieldToSelect('sold_amount', '(oi.base_row_total - oi.base_discount_amount)', array());
            if ($incrementId = $filterData->getIncrementId()) {
                $collection->addFieldToFilter('o.increment_id', $incrementId);
            }
            if ($styleNo = $filterData->getStyleNo()) {
                $collection->addFieldToFilter('t.style_no', $styleNo);
            }
            if ($styleName = $filterData->getStyleName()) {
                $collection->addFieldToFilter('t.style_name', $styleName);
            }

            $totals = Mage::getModel('adminhtml/report_item');
            $totalKeys = array();
            foreach ($this->getColumns() as $column) {
                if ($column->getTotal() == 'sum') $totalKeys[] = $column->getIndex();
            }
            foreach ($collection as $item) {
                $item->setDiscountRate($item->getBaseRowTotal() * 1 ? $item->getRowDiscountAmount() / $item->getBaseRowTotal() : 0);
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
}
