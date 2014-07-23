<?php

/**
 * Gri Adminhtml sales orders grid
 *
 * @category   Gri
 * @package    Gri_Adminhtml_Adminhtml
 * @author     Jack Yu <jack_yu@griretail.com>
 */
 
class Gri_Adminhtml_Block_Sales_Order_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('sales_order_grid');
        $this->setUseAjax(true);
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * Retrieve collection class
     *
     * @return string
     */
    protected function _getCollectionClass()
    {
        return 'sales/order_grid_collection';
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel($this->_getCollectionClass());
		$collection->getSelect()->join('order_item_quantity','`order_item_quantity`.order_id = `main_table`.entity_id',array('item_count'));
		//$collection->getSelect()->group('main_table.entity_id'); 
		//$collection->getSelect()->join('sales_flat_order', 'sales_flat_order.entity_id = main_table.entity_id', array('shipping_incl_tax','(sales_flat_order.base_subtotal + sales_flat_order.base_discount_amount - sales_flat_order.base_customer_balance_amount) AS net_sales_amount'));
		$collection->getSelect()->join('sales_flat_order', 'sales_flat_order.entity_id = main_table.entity_id', array('shipping_incl_tax','(sales_flat_order.subtotal + sales_flat_order.discount_amount - sales_flat_order.customer_balance_amount) AS net_sales_amount'));
		$collection->getSelect()->joinLeft('sales_flat_order_address',"`sales_flat_order_address`.parent_id = `main_table`.entity_id AND `sales_flat_order_address`.address_type = 'shipping'",array('country_id'));
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
   { 
;
        $this->addColumn('real_order_id', array(
            'header'=> Mage::helper('sales')->__('Order #'),
            'width' => '80px',
            'type'  => 'text',
            'index' => 'increment_id',
            'filter_index'=>'main_table.increment_id',
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'    => Mage::helper('sales')->__('Purchased From (Store)'),
                'index'     => 'store_id',
				'filter_index' => 'main_table.store_id',
                'type'      => 'store',
                'store_view'=> true,
                'display_deleted' => true,
            ));
        }

        $this->addColumn('created_at', array(
            'header' => Mage::helper('sales')->__('Order Date'),
            'index' => 'created_at',
            'type' => 'datetime',
            'width' => '100px',
			'filter_index' => 'main_table.created_at',
        ));

        /*$this->addColumn('billing_name', array(
            'header' => Mage::helper('sales')->__('Bill to Name'),
            'index' => 'billing_name',
        ));*/

        $this->addColumn('shipping_name', array(
            'header' => Mage::helper('sales')->__('Ship to Name'),
            'index' => 'shipping_name',
        ));

       $this->addColumn('country_id', array(
            'header'=> Mage::helper('sales')->__('Delivery Country'),
            'type'  => 'country',
            'index' => 'country_id',
			'width'=>'50px',
			'filter_index'=>'sales_flat_order_address.country_id',
        ));
		
		$this->addColumn('item_count',array
		(
			'header'=>Mage::helper('sales')->__('Sales Quantity'),
			'type'=>'number',
			'index'=>'item_count',
			'width'=>'50px',
			'filter_index'=>'item_count',
			'filter_condition_callback'=>array($this, '_salesQuantityFilter'),
		));
	
		$this->addColumn('net_sales_amount',array
			(
				'header'=>Mage::helper('sales')->__('Net Sales Amount'),
				'type'=>'currency',
				'index'=>'net_sales_amount',
				'width'=>'50px',
				'currency'=>'order_currency_code',
				'filter_condition_callback'=>array($this,'_netSalesAmountFilter'),
				'order_callback'=>array($this, '_netSalesAmountOrder'),
			)
		);

		$this->addColumn('shipping_incl_tax',array
		(
			'header'=>Mage::helper('sales')->__('Shipping Fee (HK$)'),
			'type'=>'currency',
			'index'=>'shipping_incl_tax',
			'width'=>'50px',
			'default'=>0,
			'currency' => 'order_currency_code',
			'filter_condition_callback'=>array($this, '_shippingFeeFilter'),
			'order_callback'=>array($this,'_shippingFeeOrder'),
			)
		);
		
        $this->addColumn('base_grand_total', array(
            'header' => Mage::helper('sales')->__('G.T. (Base)'),
            'index' => 'base_grand_total',
            'type'  => 'currency',
            'currency' => 'base_currency_code',
			'filter_index'=>'main_table.base_grand_total',
        ));

        $this->addColumn('grand_total', array(
            'header' => Mage::helper('sales')->__('G.T. (Purchased)'),
            'index' => 'grand_total',
            'type'  => 'currency',
            'currency' => 'order_currency_code',
			'filter_index' => 'main_table.grand_total',
        ));

        $this->addColumn('status', array(
            'header' => Mage::helper('sales')->__('Status'),
            'index' => 'status',
			'filter_index'=>'main_table.status',
            'type'  => 'options',
            'width' => '150px',
            'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
        ));

		$this->addColumn('updated_at',array
				(
					'header'=>Mage::helper('sales')->__('Update Time'),
					'type'=>'datetime',
					'index'=>'updated_at',
					'width'=>'100px',
					'filter_index' => 'main_table.updated_at',
					//'filter_condition_callback'=>array($this, '_updateDateFilter'),
				)
		);
		
        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
            $this->addColumn('action',
                array(
                    'header'    => Mage::helper('sales')->__('Action'),
                    'width'     => '50px',
                    'type'      => 'action',
                    'getter'     => 'getId',
                    'actions'   => array(
                        array(
                            'caption' => Mage::helper('sales')->__('View'),
                            'url'     => array('base'=>'*/sales_order/view'),
                            'field'   => 'order_id'
                        )
                    ),
                    'filter'    => false,
                    'sortable'  => false,
                    'index'     => 'stores',
                    'is_system' => true,
            ));
        }
        $this->addRssList('rss/order/new', Mage::helper('sales')->__('New Order RSS'));

        $this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel XML'));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('order_ids');
        $this->getMassactionBlock()->setUseSelectAll(false);

        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/cancel')) {
            $this->getMassactionBlock()->addItem('cancel_order', array(
                 'label'=> Mage::helper('sales')->__('Cancel'),
                 'url'  => $this->getUrl('*/sales_order/massCancel'),
            ));
        }

        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/hold')) {
            $this->getMassactionBlock()->addItem('hold_order', array(
                 'label'=> Mage::helper('sales')->__('Hold'),
                 'url'  => $this->getUrl('*/sales_order/massHold'),
            ));
        }

        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/unhold')) {
            $this->getMassactionBlock()->addItem('unhold_order', array(
                 'label'=> Mage::helper('sales')->__('Unhold'),
                 'url'  => $this->getUrl('*/sales_order/massUnhold'),
            ));
        }

        $this->getMassactionBlock()->addItem('pdfinvoices_order', array(
             'label'=> Mage::helper('sales')->__('Print Invoices'),
             'url'  => $this->getUrl('*/sales_order/pdfinvoices'),
        ));

        $this->getMassactionBlock()->addItem('pdfshipments_order', array(
             'label'=> Mage::helper('sales')->__('Print Packingslips'),
             'url'  => $this->getUrl('*/sales_order/pdfshipments'),
        ));

        $this->getMassactionBlock()->addItem('pdfcreditmemos_order', array(
             'label'=> Mage::helper('sales')->__('Print Credit Memos'),
             'url'  => $this->getUrl('*/sales_order/pdfcreditmemos'),
        ));

        $this->getMassactionBlock()->addItem('pdfdocs_order', array(
             'label'=> Mage::helper('sales')->__('Print All'),
             'url'  => $this->getUrl('*/sales_order/pdfdocs'),
        ));

        $this->getMassactionBlock()->addItem('print_shipping_label', array(
             'label'=> Mage::helper('sales')->__('Print Shipping Labels'),
             'url'  => $this->getUrl('*/sales_order_shipment/massPrintShippingLabel'),
        ));

        return $this;
    }

    public function getRowUrl($row)
    {
        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
            return $this->getUrl('*/sales_order/view', array('order_id' => $row->getId()));
        }
        return false;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

		protected function _createDateFilter($collection, $column)
	{
		if(!$value = $column->getFilter()->getValue())
		{
			return $this;
		}
		
		$from = $value['from'];
		$to = $value['to'];
		
		if(!is_null($from))
		{
			$this->getCollection()->getSelect()->where('sales_flat_order_grid.created_at >= ?',date("Y-m-d H:i:s",strtotime($from)));
		}
		if(!is_null($to))
		{
			$this->getCollection()->getSelect()->where('sales_flat_order_grid.create_at <= ?', date("Y-m-d H:i:s",strtotime($to)));
		}
		
		return $this;
	}
	
	protected function _netSalesAmountFilter($collection, $column)
	{
		if(!$value = $column->getFilter()->getValue())
		{
			return $this;
		}
		
		$from = $value['from'];
		$to = $value['to'];
		
		if(!is_null($from))
		{
			$this->getCollection()->getSelect()->where('(sales_flat_order.base_subtotal + sales_flat_order.base_discount_amount - sales_flat_order.base_customer_balance_amount) >= ?',$from);
		}
		if(!is_null($to))
		{
			$this->getCollection()->getSelect()->where('(sales_flat_order.base_subtotal + sales_flat_order.base_discount_amount - sales_flat_order.base_customer_balance_amount) <= ?',$to);
		}
		
		return $this;
	}
	
	protected function _salesQuantityFilter($collection, $column)
	{
		if(!$value = $column->getFilter()->getValue())
		{
			return $this;
		}
	
		$from = $value['from'];
		$to = $value['to'];
		if(!is_null($from))
		{
			$this->getCollection()->getSelect()->where('order_item_quantity.item_count >= ?',$from);
		}
		
		if(!is_null($to))
		{
			$this->getCollection()->getSelect()->where('order_item_quantity.item_count <= ?',$to);
		}

		return $this;
	}
	

	protected function _shippingFeeFilter($collection, $column)
	{
		if(!$value = $column->getFilter()->getValue())
		{
			return $this;
		}
		
		$from = $value['from'];
		$to = $value['to'];
		
		if(!is_null($from))
		{
			$this->getCollection()->getSelect()->where('shipping_incl_tax >= ?',$from);
		}
		
		if(!is_null($to))
		{
			$this->getCollection()->getSelect()->where('shipping_incl_tax <= ?',$to);
		}
		
		return $this;
		
	}
	
	protected function _netSalesAmountOrder($collection, $column)
	{
		$this->getCollection()->getSelect()->order('(sales_flat_order.base_subtotal + sales_flat_order.base_discount_amount - sales_flat_order.base_customer_balance_amount) '. strtoupper($column->getDir()));
	}
	
	protected function _shippingFeeOrder($collection, $column)
	{
		$this->getCollection()->getSelect()->order('shipping_incl_tax '. strtoupper($column->getDir()));
	}
	
	protected function _setCollectionOrder($column)
	{
		if ($column->getOrderCallback()) {
			call_user_func($column->getOrderCallback(), $this->getCollection(), $column);

			return $this;
		}

		return parent::_setCollectionOrder($column);
	}
}
