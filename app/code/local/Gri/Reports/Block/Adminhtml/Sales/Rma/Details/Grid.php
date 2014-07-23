<?php

/**
 * Class Gri_Reports_Block_Adminhtml_Sales_Rma_Details_Grid
 * @method Gri_Reports_Model_Resource_Report_Order_Item_Collection getCollection
 */
class Gri_Reports_Block_Adminhtml_Sales_Rma_Details_Grid extends Gri_Reports_Block_Adminhtml_Sales_Grid_Abstract
{
    protected $_resourceCollectionName = 'gri_reports/report_order_item_collection';
    protected $_rmaNoToType = array(
        'e' => 1, // Exchange
        'r' => 2, // Return
    );

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
            $collection->setIdFieldName('rma_item_id');
            $collection->join(array('r' => 'awrma/entity'), 'r.order_id=o.increment_id')
                ->addExpressionFieldToSelect('rma_item_id', 'CONCAT(oi.item_id, "-", r.id)', array())
                ->addExpressionFieldToSelect('row_price', '((oi.base_row_total - oi.base_discount_amount) / oi.qty_ordered)', array());
            if ($rmaNo = $filterData->getRmaNo()) {
                if (isset($this->_rmaNoToType[$type = strtolower($rmaNo{0})])) {
                    $collection->addFieldToFilter('r.request_type', $this->_rmaNoToType[$type]);
                    $rmaNo = substr($rmaNo, 1);
                }
                $rmaNo and $collection->addFieldToFilter('r.id', (int)$rmaNo);
            }
            if ($orderNo = $filterData->getOrderNo()) {
                $collection->addFieldToFilter('o.increment_id', $orderNo);
            }
            if ($email = $filterData->getEmail()) {
                $collection->addFieldToFilter('o.customer_email', $email);
            }

            // Re-arrange collection
            $rmaOrderItems = clone $collection;
            $rmaOrderItems->setSelect(clone $rmaOrderItems->getSelect());
            $selectRmaItems = $rmaOrderItems->getSelect();
            $selectRmaItems->reset(Zend_Db_Select::COLUMNS)
                ->columns(array('r.id', 'r.order_items'))
                ->distinct();
            $orderItems = array();

            // Pre-fetch affected order items and RMA quantities
            $qtys = array();
            foreach ($rmaOrderItems as $rmaOrderItem) {
                try {
                    $orderItems = $orderItems +
                        ($qtys[$rmaOrderItem->getData('id')] = unserialize($rmaOrderItem->getOrderItems()));
                } catch (Exception $e) {
                    continue;
                }
            }

            // Apply order item filter
            $collection->addFieldToFilter('oi.item_id', array('in' => array_keys($orderItems)));
            $totals = Mage::getModel('adminhtml/report_item');
            $totalKeys = array();
            foreach ($this->getColumns() as $column) {
                if ($column->getTotal() == 'sum') $totalKeys[] = $column->getIndex();
            }
            /* @var $rmaModel AW_Rma_Model_Entity */
            $rmaModel = Mage::getModel('awrma/entity');
            $removeItems = array();
            foreach ($collection as $item) {
                $rmaModel->setData($item->getData());
                $item->setTextId($rmaModel->getTextId());
                if (isset($qtys[$rmaModel->getId()][$item->getItemId()])) {
                    $item->setRmaQty($qty = $qtys[$rmaModel->getId()][$item->getItemId()])
                        ->setRmaAmount($item->getRowPrice() * $qty);
                } else {
                    $removeItems[] = $item->getId();
                }
                foreach ($totalKeys as $key) {
                    $totals->setData($key, $totals->getData($key) + $item->getData($key));
                }
            }
            foreach ($removeItems as $key) $collection->removeItemByKey($key);
            $this->setTotals($totals);
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

        $this->addColumn('text_id', array(
            'header' => $this->__('RMA #'),
            'index' => 'text_id',
            'sortable' => FALSE,
        ));

        $this->addColumn('request_type', array(
            'header' => $this->__('Return Request Type'),
            'index' => 'request_type',
            'type' => 'options',
            'options' => Mage::getBlockSingleton('awrma/adminhtml_rma_edit_tab_requestinformation')->getTypesOptions(),
            'sortable' => FALSE,
        ));

        $this->addColumn('name', array(
            'header' => $this->__('Product Name'),
            'index' => 'name',
            'sortable' => FALSE,
        ));

        $this->addColumn('sku', array(
            'header' => $this->__('Product No.'),
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

        $this->addColumn('rma_qty', array(
            'header' => $this->__('RMA Qty'),
            'index' => 'rma_qty',
            'sortable' => FALSE,
            'type' => 'number',
            'total' => 'sum',
        ));

        $currency = (string)Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE);
        $this->addColumn('rma_amount', array(
            'header' => $this->__('RMA Amount'),
            'index' => 'rma_amount',
            'sortable' => FALSE,
            'type' => 'currency',
            'currency_code' => $currency,
            'total' => 'sum',
        ));

        $this->addColumn('status', array(
            'header' => $this->__('RMA Status'),
            'index' => 'status',
            'type' => 'options',
            'options' => Mage::getBlockSingleton('awrma/adminhtml_rma_edit_tab_requestinformation')->getStatusOptions(),
            'sortable' => FALSE,
        ));

        $this->addColumn('increment_id', array(
            'header' => $this->__('Order #'),
            'index' => 'increment_id',
            'sortable' => FALSE,
        ));

        $this->addColumn('payment_account', array(
            'header' => $this->__('Payment Account'),
            'index' => 'payment_account',
            'sortable' => FALSE,
        ));

        $this->addExportType('*/*/exportRmaDetailsExcel', Mage::helper('adminhtml')->__('Excel XML'));
        $this->addExportType('*/*/exportRmaDetailsCsv', Mage::helper('adminhtml')->__('CSV'));

        return parent::_prepareColumns();
    }
}
