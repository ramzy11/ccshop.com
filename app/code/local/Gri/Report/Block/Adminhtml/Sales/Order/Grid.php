<?php

/**
 * Class Gri_Reports_Block_Adminhtml_Sales_Order_Grid
 */
class Gri_Reports_Block_Adminhtml_Sales_Order_Grid extends Gri_Reports_Block_Adminhtml_Sales_Grid_Abstract
{

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
            'header' => $this->__('Order #'),
            'index' => 'increment_id',
            'sortable' => FALSE,
        ));

        $this->addColumn('total_qty_ordered', array(
            'header' => $this->__('Qty Ordered'),
            'index' => 'total_qty_ordered',
            'type' => 'number',
            'total' => 'sum',
            'sortable' => FALSE,
        ));

        $currency = (string)Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE);
        $this->addColumn('base_subtotal', array(
            'header' => $this->__('Subtotal'),
            'index' => 'base_subtotal',
            'type' => 'currency',
            'currency_code' => $currency,
            'total' => 'sum',
            'sortable' => FALSE,
        ));

        $this->addColumn('coupon_code', array(
            'header' => $this->__('Coupon Code'),
            'index' => 'coupon_code',
            'sortable' => FALSE,
        ));

        $this->addColumn('order_discount_amount', array(
            'header' => $this->__('Order Discount Amount'),
            'index' => 'order_discount_amount',
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

        $this->addColumn('net_sales_totals', array(
            'header' => $this->__('Net Sales Total'),
            'index' => 'net_sales_totals',
            'type' => 'currency',
            'currency_code' => $currency,
            'total' => 'sum',
            'sortable' => FALSE,
        ));

        $this->addColumn('base_shipping_amount', array(
            'header' => $this->__('Shipping Fee'),
            'index' => 'base_shipping_amount',
            'type' => 'currency',
            'currency_code' => $currency,
            'total' => 'sum',
            'sortable' => FALSE,
        ));

        $this->addColumn('base_grand_total', array(
            'header' => $this->__('Money Received $'),
            'index' => 'base_grand_total',
            'type' => 'currency',
            'currency_code' => $currency,
            'total' => 'sum',
            'sortable' => FALSE,
        ));

        $this->addColumn('base_total_paid', array(
            'header' => $this->__('Total Paid $'),
            'index' => 'base_total_paid',
            'type' => 'currency',
            'currency_code' => $currency,
            'total' => 'sum',
            'sortable' => FALSE,
        ));

        $this->addColumn('status', array(
            'header' => $this->__('Order Status'),
            'index' => 'status',
            'type' => 'options',
            'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
            'sortable' => FALSE,
        ));

        $this->addColumn('carrier', array(
            'header' => $this->__('Carrier'),
            'index' => 'carrier',
            'sortable' => FALSE,
        ));

        $this->addColumn('track_number', array(
            'header' => $this->__('Tracking #'),
            'index' => 'track_number',
            'sortable' => FALSE,
        ));

        $this->addColumn('method', array(
            'header' => $this->__('Payment Method'),
            'index' => 'method',
            'sortable' => FALSE,
        ));

        $this->addColumn('txn_id', array(
            'header' => $this->__('Transaction ID'),
            'index' => 'txn_id',
            'sortable' => FALSE,
        ));

        $this->addColumn('payment_account', array(
            'header' => $this->__('Payment Account'),
            'index' => 'payment_account',
            'sortable' => FALSE,
        ));

        $this->addColumn('customer_email', array(
            'header' => $this->__('Customer Email'),
            'index' => 'customer_email',
            'sortable' => FALSE,
        ));

        $this->addColumn('recipient', array(
            'header' => $this->__('Recipient'),
            'index' => 'recipient',
            'sortable' => FALSE,
        ));

		$this->addColumn('country_id',array(
			'header'=>$this->__('Ship To Country'),
			'index'=>'country_id',
			'type'=>'country',
		));
		
        $this->addColumn('region', array(
            'header' => $this->__('Province'),
            'index' => 'region',
            'sortable' => FALSE,
        ));

        $this->addColumn('city', array(
            'header' => $this->__('City'),
            'index' => 'city',
            'sortable' => FALSE,
        ));

        $this->addColumn('street', array(
            'header' => $this->__('Address'),
            'index' => 'street',
            'sortable' => FALSE,
        ));

        $this->addColumn('telephone', array(
            'header' => $this->__('Telephone No.'),
            'index' => 'telephone',
            'sortable' => FALSE,
        ));

        $groups = Mage::getResourceModel('customer/group_collection')
            ->addFieldToFilter('customer_group_id', array('gt' => 0))
            ->load()
            ->toOptionHash();
        $this->addColumn('customer_group_id', array(
            'header' => $this->__('VIP Group'),
            'index' => 'customer_group_id',
            'type' => 'options',
            'options' => $groups,
            'sortable' => FALSE,
        ));

        $this->addColumn('created_at', array(
            'header' => $this->__('Order Created At'),
            'index' => 'created_at',
            'sortable' => FALSE,
            'type' => 'datetime',
        ));

        $this->addColumn('order_paid_at', array(
            'header' => $this->__('Paid At'),
            'index' => 'order_paid_at',
            'sortable' => FALSE,
            'type' => 'datetime',
        ));

        $this->addColumn('shipped_at', array(
            'header' => $this->__('Shipped At'),
            'index' => 'shipped_at',
            'sortable' => FALSE,
            'type' => 'datetime',
        ));

        $this->addColumn('canceled_at', array(
            'header' => $this->__('Canceled At'),
            'index' => 'canceled_at',
            'sortable' => FALSE,
            'type' => 'datetime',
        ));

        $this->addExportType('*/*/exportOrdersExcel', Mage::helper('adminhtml')->__('Excel XML'));
        $this->addExportType('*/*/exportOrdersCsv', Mage::helper('adminhtml')->__('CSV'));

        return parent::_prepareColumns();
    }

    protected function _prepareCollection()
    {
        parent::_prepareCollection();
        $filterData = $this->getFilterData();
        $collection = $this->getCollection();
        if ($collection) {
            $collection->join(array('sop' => 'sales/order_payment'), 't.order_id=sop.parent_id', array(
                'method',
                'alipay_pay_method',
                'alipay_pay_bank',
            ))->joinLeft(array('oa' => 'sales/order_address'), 'o.shipping_address_id=oa.entity_id', array(
                'address_firstname' => 'firstname',
                'address_lastname' => 'lastname',
                'country_id',
                'region',
                'city',
                'postcode',
                'street',
                'telephone',
            ))->joinLeft(array('op' => 'sales/order_payment'), 'op.parent_id=t.order_id', array(
                'additional_information',
            ))->joinLeft(array('pt' => 'sales/payment_transaction'), 'pt.order_id=t.order_id AND txn_type = "capture"', array(
                'txn_id',
            ))->addExpressionFieldToSelect('order_discount_amount', 'ABS(o.base_discount_amount)', array());
            if ($incrementId = $filterData->getIncrementId()) {
                $collection->addFieldToFilter('o.increment_id', $incrementId);
            }
            if ($email = $filterData->getEmail()) {
                $collection->addFieldToFilter('o.customer_email', $email);
            }
            if ($couponCode = $filterData->getCouponCode()) {
                $collection->addFieldToFilter('o.coupon_code', $couponCode);
            }

            $totals = Mage::getModel('adminhtml/report_item');
            $totalKeys = array();
            foreach ($this->getColumns() as $column) {
                if ($column->getTotal() == 'sum') $totalKeys[] = $column->getIndex();
            }
            foreach ($collection as $item) {
                $item->setRecipient($item->getAddressLastname() . ' ' . $item->getAddressFirstname());
                $item->setDiscountRate($item->getBaseSubtotal() * 1 ? $item->getOrderDiscountAmount() / $item->getBaseSubtotal() : 0);
                $item->setNetSalesTotals($item->getBaseSubtotal() - $item->getOrderDiscountAmount());
                $this->formatItemPaymentMethod($item);
                if ($additionalInformation = $item->getAdditionalInformation()) {
                    $additionalInformation = unserialize($additionalInformation);
                    isset($additionalInformation['paypal_payer_email']) and
                        $item->setPaymentAccount($additionalInformation['paypal_payer_email']);
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

    public function formatItemPaymentMethod(Varien_Object $item, $key = 'method')
    {
        $method = Mage::getStoreConfig('payment/' . $item->_getData($key) . '/title', $item->getStoreId());
        if (($bank = $item->getAlipayPayBank()) && $bank != 'ALIPAY') {
            $method .= ': ' . Mage::getSingleton('alipay/source_bank')->getLabel($bank);
        }
        $method and $item->setData($key, $method);
        return $this;
    }
}
