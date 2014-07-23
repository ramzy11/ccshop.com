<?php
/**
 * Adminhtml customer grid block extended
 *
 * @category   Gri
 * @package    Gri_Adminhtml
 * @author     Jack Yu @ GRI <jack_yu@griretail.com>
 */
class Gri_Adminhtml_Block_Customer_Grid extends Mage_Adminhtml_Block_Customer_Grid 
{
	public function setCollection($collection)
	{
		$collection->joinTable(array('cll'=>'customer_last_login'),'customer_id = entity_id', array('last_login'), null, 'left');
		$collection->getSelect()->joinLeft(array('ss'=>'sales_stat'), "ss.customer_id = e.entity_id",array('ss.lifetime_sales',"ss.average_sales"));
		//$collection->getSelect()->group('e.entity_id');
		$collection->addAttributeToSelect('login_at');
		parent::setCollection($collection);
	}
	
	protected function _prepareCollection()
	{
			$filter = $this->getParam($this->getVarNameFilter(), null);
			if(is_string($filter))
			{
				$data = $this->helper('adminhtml')->prepareFilterString($filter);
				$this->_setFilterValues($data);
			}
			else if(is_array($filter))
			{
				$this->_setFilterValues($filter);
			}
			
			return parent::_prepareCollection();
	}
	
    protected function _prepareColumns()
    {
		parent::_prepareColumns();
	
        $this->addColumnAfter('login_at', array(
            'header'    => Mage::helper('customer')->__('Last logged in'),
			//'filter'	=> false,
            'width'     => '50px',
            'index'     => 'last_login',
			'default'	=> '<i>Never logged in</i>',
            'type'  	=> 'datetime',
			'gmtoffset' => true,
			'filter_condition_callback' => array($this, '_lastLoginFilter'),
			'order_callback' => array($this, '_lastLoginOrder'),
        ),'customer_since');

		$this->addColumnAfter('lifetime_sales', array(
			'header'	=> Mage::helper('customer')->__('Lifetime Sales (HK$)'),
			'type'		=> 'currency',
			//'filter'	=> false,
			'default'	=> '0.00',
			'width'		=> '80px',
			'index'		=> 'lifetime_sales',
			'filter_index'=> 'ss.lifetime_sales',
			'filter_condition_callback' => array($this, '_lifetimeSalesFilter'),
			'order_callback' => array($this, '_lifetimeSalesOrder'),
		),'login_at');
		
		$this->addColumnAfter('average_sales', array(
			'header'	=> Mage::helper('customer')->__('Average Sales (HK$)'),
			'type'		=> 'currency',
			//'filter'	=> false,
			'default'	=> '$0.00',
			'width'		=> '80px',
			'index'		=> 'average_sales',
			'filter_index'=> 'ss.average_sales',
			'filter_condition_callback' => array($this, '_averageSalesFilter'),
			'order_callback' => array($this,'_averageSalesOrder'),
		),'lifetime_sales');
		
		$this->sortColumnsByOrder();
		
        return $this;
    }
	
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=> true));
    }	
	
	protected function _lastLoginFilter($collection, $column)
	{
		if(!$value = $column->getFilter()->getValue())
		{
			return $this;
		}
		
		$from = $value['orig_from'];
		$to = $value['orig_to'];
		
		if($from != "")
		{
			$this->getCollection()->getSelect()->where("cll.last_login >= ?",date("Y-m-d 00:00:00", strtotime($from)));
		}
		
		if($to != "")
		{
			$this->getCollection()->getSelect()->where("cll.last_login <= ?",date("Y-m-d 23:59:59",strtotime($to)));
		}
		
		//$this->getcollection()->getSelect()->__toString();
		
		return $this;
	}
	
	protected function _lastLoginOrder($collection, $column)
	{
		$this->getCollection()->getSelect()->order('cll.last_login ' . strtoupper($column->getDir()));
		return $this;
	}
	
	protected function _lifetimeSalesFilter($collection, $column)
	{
		if(!$value = $column->getFilter()->getValue())
		{
			return $this;
		}
		
		$from = $value['from'];
		$to = $value['to'];

		$this->getCollection()->getSelect()->where("ss.lifetime_sales IS NOT NULL");
		
		if($from != "")
		{
			$this->getCollection()->getSelect()->where("ss.lifetime_sales >= ?",$from);
		}

		
		if($to != "")
		{
			$this->getCollection()->getSelect()->where("ss.lifetime_sales <= ?",$to );
		}
		
		return $this;
	}
	
	protected function _lifetimeSalesOrder($collection, $column)
	{
		$this->getCollection()->getSelect()->order('ss.lifetime_sales ' . strtoupper($column->getDir()));
		return $this;
	}
	
	protected function _averageSalesFilter($collection, $column)
	{
		if(!$value = $column->getFilter()->getValue())
		{
			return $this;
		}
	
		$from = $value['from'];
		$to = $value['to'];
		
		if($from != "")
		{
			$this->getCollection()->getSelect()->where("ss.average_sales >= ?",$from );
		}

		
		if($to != "")
		{
			$this->getCollection()->getSelect()->where("ss.average_sales <= ?",$to);
		}
	
		return $this;
	}
	
	protected function _averageSalesOrder($collection, $column)
	{
		//var_dump($column->getIndex());
		$this->getCollection()->getSelect()->order('ss.average_sales ' . strtoupper($column->getDir()));
		return $this;
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
