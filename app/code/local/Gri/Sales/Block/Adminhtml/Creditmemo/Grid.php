<?php

/**
 * Class Gri_Sales_Block_Adminhtml_Creditmemo_Grid
 * @method Mage_Sales_Model_Resource_Order_Creditmemo_Grid_Collection getCollection()
 */
class Gri_Sales_Block_Adminhtml_Creditmemo_Grid extends Mage_Adminhtml_Block_Sales_Creditmemo_Grid
{

    protected function _prepareCollection()
    {
        $export = $this->_isExport;
        $this->_isExport = TRUE;
        parent::_prepareCollection();
        $collection = $this->getCollection();
        $collection->join(array('o' => 'sales/order'),
            'o.entity_id=main_table.order_id',
            array(
                'order_status' => 'status',
            )
        );
        $collection->addExpressionFieldToSelect('base_money_refunded', '(main_table.base_grand_total - main_table.base_customer_balance_total_refunded)', array());
        $select = $collection->getSelect();
        $select->joinLeft(
            array('pt' => $collection->getTable('sales/payment_transaction')),
            'pt.order_id=main_table.order_id AND pt.txn_type = "capture"',
            array(
                'txn_id',
            )
        );

        if (!($this->_isExport = $export)) {
            $this->getCollection()->load();
            $this->_afterLoadCollection();
        }
        return $this;
    }

    protected function _prepareColumns()
    {
        parent::_prepareColumns();

        $this->removeColumn('order_created_at');
        $this->removeColumn('grand_total');
        $this->getColumn('created_at')->setType('date');
        $this->getColumn('state')->setHeader($this->__('CM Status'));
        foreach ($this->getColumns() as $column) {
            $column->getFilterIndex() or $column->setFilterIndex('main_table.' . $column->getIndex());
        }

        $this->addColumnAfter('txn_id', array(
            'header' => $this->__('Transaction ID'),
            'index' => 'txn_id',
            'filter_index' => 'pt.txn_id',
            'type' => 'text',
        ), 'order_increment_id');

        $this->addColumnAfter('order_status', array(
            'header' => $this->__('Order Status'),
            'index' => 'order_status',
            'filter_index' => 'o.status',
            'type' => 'options',
            'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
        ), 'billing_name');

        $this->addColumnAfter('base_subtotal', array(
            'header' => $this->__('Net Sales Total $'),
            'index' => 'base_subtotal',
            'filter_index' => 'main_table.base_subtotal',
            'type' => 'currency',
            'align' => 'right',
            'currency' => 'base_currency_code',
        ), 'state');

        $this->addColumnAfter('base_shipping_amount', array(
            'header' => $this->__('Shipping Fee $'),
            'index' => 'base_shipping_amount',
            'filter_index' => 'main_table.base_shipping_amount',
            'type' => 'currency',
            'align' => 'right',
            'currency' => 'base_currency_code',
        ), 'base_subtotal');

        $this->addColumnAfter('base_grand_total', array(
            'header' => $this->__('Total Refunded $'),
            'index' => 'base_grand_total',
            'filter_index' => 'main_table.base_grand_total',
            'type' => 'currency',
            'align' => 'right',
            'currency' => 'base_currency_code',
        ), 'base_shipping_amount');

        $this->addColumnAfter('refunded_at', array(
            'header' => $this->__('Refunded At'),
            'index' => 'refunded_at',
            'type' => 'datetime',
        ), 'base_grand_total');

        $this->addColumnAfter('base_money_refunded', array(
            'header' => $this->__('Money Refunded $'),
            'index' => 'base_money_refunded',
            'filter' => FALSE,
            'type' => 'currency',
            'align' => 'right',
            'currency' => 'base_currency_code',
        ), 'refunded_at');

        $this->addColumnAfter('base_customer_balance_total_refunded', array(
            'header' => $this->__('Refunded to<br/>Store Credit'),
            'index' => 'base_customer_balance_total_refunded',
            'type' => 'currency',
            'align' => 'right',
            'currency' => 'base_currency_code',
        ), 'base_money_refunded');

        $this->sortColumnsByOrder();
        return $this;
    }

    public function addColumn($columnId, $column)
    {
        if ($this->_isExport && isset($column['type']) && $column['type'] == 'currency') {
            $column['type'] = 'number';
        }
        return parent::addColumn($columnId, $column);
    }
}
