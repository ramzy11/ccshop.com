<?php

/**
 * Class Gri_Reports_Block_Adminhtml_Sales_Financial_Grid
 */
class Gri_Reports_Block_Adminhtml_Sales_Financial_Grid extends Gri_Reports_Block_Adminhtml_Sales_Grid_Abstract
{

    protected function _prepareCollection()
    {
        parent::_prepareCollection();
        $filterData = $this->getFilterData();
        $collection = $this->getCollection();
        if ($collection) {
            $this->setCountTotals(TRUE);
            $this->setCountSubTotals(FALSE);

            $collection->setPeriodField($filterData->getReportType());
            $collection->addExpressionFieldToSelect('date', 'shipped_at', array())
                ->addExpressionFieldToSelect('order_discount_amount', 'ABS(o.base_discount_amount)', array())
                ->addFieldToFilter('t.order_paid_at', array('notnull' => TRUE));
            $collection->joinLeft(array('oa' => 'sales/order_address'), 'o.shipping_address_id=oa.entity_id', array(
                'address_firstname' => 'firstname',
                'address_lastname' => 'lastname',
                'country_id',
                'region',
                'city',
                'postcode',
                'street',
                'telephone',
            ))->joinLeft(array('pt' => 'sales/payment_transaction'),
                'pt.order_id=t.order_id AND txn_type = "capture" AND pt.parent_id IS NULL',
                array(
                    'txn_id',
                )
            );
            if ($txnId = $filterData->getTxnId()) {
                $collection->addFieldToFilter('pt.txn_id', array('like' => $txnId . '%'));
            }
            if ($orderIncrementId = $filterData->getOrderId()) {
                $collection->addFieldToFilter('o.increment_id', $orderIncrementId);
            }
            if ($recipient = $filterData->getRecipient()) {
                $where = array();
                $select = $collection->getSelect();
                foreach (explode(' ', $recipient) as $r) {
                    $v = $r . '%';
                    $where[] = $select->getAdapter()->quoteInto('oa.firstname LIKE (?)', $v);
                    $where[] = $select->getAdapter()->quoteInto('oa.lastname LIKE (?)', $v);
                }
                $select->where(implode(' OR ', $where));
            }
            $collection->setColumnGroupBy(NULL);
            $creditmemoCollection = clone $collection;
            $creditmemoSelect = clone $creditmemoCollection->getSelect();
            $creditmemoCollection->setSelect($creditmemoSelect);
            $creditmemoCollection->join(array('oi' => 'sales/order_item'), 'oi.order_id=t.order_id AND oi.parent_item_id IS NULL', array())
                ->join(array('c' => 'sales/creditmemo'), 'c.order_id=t.order_id')
                ->joinLeft(array('ci' => 'sales/creditmemo_item'), 'ci.parent_id=c.entity_id AND ci.order_item_id=oi.item_id', array());
            $creditmemoCollection->setPeriodField('c.refunded_at');
            $creditmemoCollection->addExpressionFieldToSelect('date', 'c.refunded_at', array())
                ->addExpressionFieldToSelect('order_discount_amount', 'ABS(c.base_discount_amount)', array())
                ->addExpressionFieldToSelect('base_total_paid', 'c.base_grand_total', array())
                ->addExpressionFieldToSelect('increment_id', 'o.increment_id', array())
                ->addExpressionFieldToSelect('total_qty_ordered', 'IFNULL(SUM(ci.qty), 0)', array());
            $creditmemoSelect->group('c.entity_id');
            $totals = Mage::getModel('adminhtml/report_item');
            $totalKeys = array();
            foreach ($this->getColumns() as $column) {
                if ($column->getTotal() == 'sum') $totalKeys[] = $column->getIndex();
            }
            $data = array();
            foreach ($collection as $item) {
                $data[$item->getDate() . 't1'] = $item;
            }
            foreach ($creditmemoCollection as $item) {
                $data[$item->getDate() . 't2'] = $item;
                $item->setBaseSubtotal($item->getBaseSubtotal() * -1)
                    ->setBaseGrandTotal(($item->getBaseGrandTotal() - $item->getBaseCustomerBalanceTotalRefunded()) * -1)
                    ->setOrderDiscountAmount($item->getOrderDiscountAmount() * -1)
                    ->setBaseShippingAmount($item->getBaseShippingAmount() * -1)
                    ->setBaseTotalPaid($item->getBaseTotalPaid() * -1)
                    ->setTotalQtyOrdered($item->getTotalQtyOrdered() * -1);
            }
            ksort($data);
            $collection->clear();
            $collection->setIsLoaded();
            foreach ($data as $item) {
                $item->setRecipient($item->getAddressFirstname() . ' ' . $item->getAddressLastname());
                $item->setDiscountRate($item->getBaseSubtotal() * 1 ? $item->getOrderDiscountAmount() / $item->getBaseSubtotal() : 0);
                $item->setNetSalesTotals($item->getBaseSubtotal() - $item->getOrderDiscountAmount());
                $collection->addItem($item);
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
        $this->addColumn('date', array(
            'header' => Mage::helper('sales')->__('Date'),
            'index' => 'date',
            'width' => 100,
            'sortable' => FALSE,
            'type' => 'date',
            'totals_label' => Mage::helper('sales')->__('Total'),
            'subtotals_label' => Mage::helper('sales')->__('Subtotal'),
            'html_decorators' => array('nobr'),
        ));

        $this->addColumn('txn_id', array(
            'header' => $this->__('Transaction ID'),
            'index' => 'txn_id',
            'sortable' => FALSE,
        ));

        $this->addColumn('total_qty_ordered', array(
            'header' => $this->__('Sales Qty'),
            'index' => 'total_qty_ordered',
            'type' => 'number',
            'total' => 'sum',
            'sortable' => FALSE,
        ));

        $currency = (string)Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE);
        $this->addColumn('base_subtotal', array(
            'header' => $this->__('Sales Amount $'),
            'index' => 'base_subtotal',
            'sortable' => FALSE,
            'type' => 'currency',
            'currency_code' => $currency,
            'total' => 'sum',
        ));

        $this->addColumn('order_discount_amount', array(
            'header' => $this->__('Discount Amount $'),
            'index' => 'order_discount_amount',
            'sortable' => FALSE,
            'type' => 'currency',
            'currency_code' => $currency,
            'total' => 'sum',
        ));

        $percentageRenderer = 'gri_reports/adminhtml_sales_grid_column_renderer_percentage';
        $this->addColumn('discount_rate', array(
            'header' => $this->__('Discount %'),
            'index' => 'discount_rate',
            'sortable' => FALSE,
            'type' => 'percentage',
            'renderer' => $percentageRenderer,
        ));

        $this->addColumn('net_sales_totals', array(
            'header' => $this->__('Net Sales Total $'),
            'index' => 'net_sales_totals',
            'sortable' => FALSE,
            'type' => 'currency',
            'currency_code' => $currency,
            'total' => 'sum',
        ));

        $this->addColumn('base_shipping_amount', array(
            'header' => $this->__('Shipping Fee $'),
            'index' => 'base_shipping_amount',
            'sortable' => FALSE,
            'type' => 'currency',
            'currency_code' => $currency,
            'total' => 'sum',
        ));

        $this->addColumn('base_grand_total', array(
            'header' => $this->__('Money Received / Refunded $'),
            'index' => 'base_grand_total',
            'sortable' => FALSE,
            'type' => 'currency',
            'currency_code' => $currency,
            'total' => 'sum',
        ));

        $this->addColumn('base_total_paid', array(
            'header' => $this->__('Total Paid / Refunded $'),
            'index' => 'base_total_paid',
            'sortable' => FALSE,
            'type' => 'currency',
            'currency_code' => $currency,
            'total' => 'sum',
        ));

        $this->addColumn('status', array(
            'header' => $this->__('Order Status'),
            'index' => 'status',
            'type' => 'options',
            'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
            'sortable' => FALSE,
        ));

        $this->addColumn('increment_id', array(
            'header' => $this->__('Order ID'),
            'index' => 'increment_id',
            'sortable' => FALSE,
        ));

        $this->addColumn('recipient', array(
            'header' => $this->__('Ship to Name'),
            'index' => 'recipient',
            'sortable' => FALSE,
        ));

        $this->addExportType('*/*/exportFinancialExcel', Mage::helper('adminhtml')->__('Excel XML'));
        $this->addExportType('*/*/exportFinancialCsv', Mage::helper('adminhtml')->__('CSV'));

        return parent::_prepareColumns();
    }
}
