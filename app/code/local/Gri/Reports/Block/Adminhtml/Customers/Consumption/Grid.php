<?php

/**
 * Class Gri_Reports_Block_Adminhtml_Sales_Summary_Grid
 * @method Gri_Reports_Model_Resource_Report_Order_Item_Collection getCollection
 */
class Gri_Reports_Block_Adminhtml_Customers_Consumption_Grid extends Gri_Reports_Block_Adminhtml_Sales_Grid_Abstract
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

        $this->addColumn('customer_email', array(
            'header' => $this->__('Username'),
            'index' => 'customer_email',
            'sortable' => FALSE,
        ));

        $this->addColumn('valid_orders', array(
            'header' => $this->__('Valid Orders'),
            'index' => 'valid_orders',
            'sortable' => FALSE,
            'type' => 'number',
            'total' => 'sum',
        ));

        $this->addColumn('qty_actually_sold', array(
            'header' => $this->__('Qty Actually Sold by Customers'),
            'index' => 'qty_actually_sold',
            'sortable' => FALSE,
            'type' => 'number',
            'total' => 'sum',
        ));

        $currency = (string)Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE);
        $this->addColumn('actual_amount', array(
            'header' => $this->__('Total Actual Amount'),
            'index' => 'actual_amount',
            'sortable' => FALSE,
            'type' => 'currency',
            'currency_code' => $currency,
            'total' => 'sum',
        ));

        $this->addColumn('region', array(
            'header' => $this->__('State/Province'),
            'index' => 'region',
            'sortable' => FALSE,
        ));

        $this->addColumn('city', array(
            'header' => $this->__('City'),
            'index' => 'city',
            'sortable' => FALSE,
        ));

        $this->addColumn('street', array(
            'header' => $this->__('Street Address'),
            'index' => 'street',
            'sortable' => FALSE,
        ));

        $this->addColumn('telephone', array(
            'header' => $this->__('Telephone'),
            'index' => 'telephone',
            'sortable' => FALSE,
        ));

        $groups = Mage::getResourceModel('customer/group_collection')
            ->addFieldToFilter('customer_group_id', array('gt'=> 0))
            ->load()
            ->toOptionHash();
        $this->addColumn('max_customer_group_id', array(
            'header'    => $this->__('Customer Group'),
            'index'     => 'max_customer_group_id',
            'type'      =>  'options',
            'options'   =>  $groups,
            'sortable'  => FALSE,
        ));

        $this->addColumn('first_consumption_at', array(
            'header' => $this->__('First Consumption At'),
            'index' => 'first_consumption_at',
            'sortable' => FALSE,
        ));

        $this->addColumn('last_consumption_at', array(
            'header' => $this->__('Last Consumption At'),
            'index' => 'last_consumption_at',
            'sortable' => FALSE,
        ));

        $this->addExportType('*/*/exportConsumptionSummaryExcel', Mage::helper('adminhtml')->__('Excel XML'));
        $this->addExportType('*/*/exportConsumptionSummaryCsv', Mage::helper('adminhtml')->__('CSV'));

        return parent::_prepareColumns();
    }

    protected function _prepareCollection()
    {
        parent::_prepareCollection();
        $filterData = $this->getFilterData();
        $collection = $this->getCollection();
        if ($collection) {
            $collection->addExpressionFieldToSelect('qty_actually_sold', 'SUM(oi.qty_ordered - oi.qty_refunded)', array())
                ->addExpressionFieldToSelect('actual_amount', 'SUM(oi.base_row_total - oi.base_discount_amount - oi.base_amount_refunded + IFNULL(oi.base_discount_refunded, 0))', array())
                ->addExpressionFieldToSelect('max_customer_group_id', 'MAX(o.customer_group_id)', array())
                ->addExpressionFieldToSelect('valid_orders', 'COUNT(DISTINCT o.entity_id)', array())
                ->addFieldToFilter('o.customer_id', array('notnull' => 1))
                ->join(array('i' => 'sales/invoice'), 'i.order_id=o.entity_id', array()) // Ensure order is invoiced
                ->setOrder($this->getPeriodField(), $collection::SORT_ORDER_ASC)
                ->setOrder('actual_amount', $collection::SORT_ORDER_DESC);
            $collection->getSelect()->where('oi.qty_shipped > oi.qty_refunded');
            if ($email = $filterData->getEmail()) {
                $collection->addFieldToFilter('o.customer_email', $email);
            }
            $groupBy = array(
                $this->getPeriodField(),
                'o.customer_id',
            );
            $collection->getSelect()->group($groupBy);
            $totals = Mage::getModel('adminhtml/report_item');
            $totalKeys = array();
            foreach ($this->getColumns() as $column) {
                if ($column->getTotal() == 'sum') $totalKeys[] = $column->getIndex();
            }
            $customers = array();
            foreach ($collection as $item) {
                $customers[$item->getCustomerId()] = array();
                foreach ($totalKeys as $key) {
                    $totals->setData($key, $totals->getData($key) + $item->getData($key));
                }
            }

            // Fetch first_consumption_at and last_consumption_at
            $invoiceSelect = clone $collection->getSelect();
            $invoiceSelect->reset();
            $invoiceSelect->from(array('i' => $collection->getTable('sales/invoice')), array(
                'first_consumption_at' => 'MIN(i.created_at)',
                'last_consumption_at' => 'MAX(i.created_at)',
            ))->join(array('o' => $collection->getTable('sales/order')), 'o.entity_id=i.order_id', array('customer_id'))
                ->where('o.customer_id IN (?)', array_keys($customers))
                ->group(array('o.customer_id'));
            $invoiceData = $invoiceSelect->query()->fetchAll();
            foreach ($invoiceData as $row) {
                $customers[$row['customer_id']] += $row;
            }

            // Fetch latest shipping address ids
            $addressSelect = clone $collection->getSelect();
            $addressSelect->reset();
            $addressSelect->from($addressTable = array('a' => $collection->getTable('sales/order_address')), array(
                'entity_id' => 'MAX(a.entity_id)',
            ))->where('a.customer_id IN (?)', array_keys($customers))
                ->where('a.address_type = ?', 'shipping')
                ->group(array('a.customer_id'));
            $addressData = $addressSelect->query()->fetchAll();
            $addresses = array();
            foreach ($addressData as $row) $addresses[] = $row['entity_id'];

            // Fetch detailed latest shipping address
            $addressSelect->reset()->from($addressTable)->where('a.entity_id IN (?)', $addresses);
            $addressData = $addressSelect->query()->fetchAll();
            foreach ($addressData as $row) {
                $customers[$row['customer_id']] += $row;
            }

            // Combine first_consumption_at, last_consumption_at and addresses into collection
            foreach ($collection as $item) if (isset($customers[$item->getCustomerId()])) {
                $item->addData($customers[$item->getCustomerId()]);
            }
            $this->setTotals($totals);
        } else {
            $this->setCollection(Mage::getModel('reports/grouped_collection'));
        }
        return $this;
    }

}
