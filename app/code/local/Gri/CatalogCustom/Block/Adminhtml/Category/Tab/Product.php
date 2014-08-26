<?php
class Gri_CatalogCustom_Block_Adminhtml_Category_Tab_Product extends Mage_Adminhtml_Block_Catalog_Category_Tab_Product
{
    /**
     * Add column 'best seller' after 'price' column
     * @see Mage_Adminhtml_Block_Catalog_Category_Tab_Product::_prepareColumns()
     */
    public function _prepareColumns()
    {
		parent::_prepareColumns();
        $this->addColumnAfter('best_seller', array(
            'header' => Mage::helper('catalog')->__('Best Seller'),
            'index' => 'best_seller',
            'width' => '100',
            'type' => 'number',
			'filter_condition_callback'=>array($this,'_bsFilter'),
			'order_callback'=>array($this, '_bsOrder'),
        ), 'price');

        $this->addColumnAfter('editors_pick', array(
            'header' => Mage::helper('catalog')->__('Editor\'s Pick'),
            'index' => 'editors_pick',
            'width' => '100',
            'type' => 'text',
			'filter_condition_callback'=>array($this,'_epFilter'),
			'order_callback'=>array($this, '_epOrder'),
        ), 'price');
		
        $this->addColumnAfter('type', array(
                'header' => Mage::helper('catalog')->__('Type'),
                'width' => '60px',
                'index' => 'type_id',
                'type' => 'options',
                'options' => Mage::getSingleton('catalog/product_type')->getOptionArray(),
        ), 'sku');
		
        $this->addColumnAfter('ref_no', array(
            'header' => Mage::helper('catalog')->__('Ref. No.'),
            'index' => 'ref_no',
            'width' => '100',
            'type' => 'text',
			'filter_condition_callback'=>array($this,'_rnFilter'),
			'order_callback'=>array($this, '_rnOrder'),
        ), 'name');
		
		$this->addColumnAfter('special_price', array(
            'header' => Mage::helper('catalog')->__('Special Price'),
            'index' => 'special_price',
            'width' => '100',
            'type' => 'currency',
			'currency_code'=> 'base_currency_code',
			'filter_condition_callback'=>array($this,'_spFilter'),
			'order_callback'=>array($this, '_spOrder'),
        ), 'price');
        // Experimental filters
        if (Mage::getSingleton('admin/session')->getUser()->getUsername() == 'admin') {


            $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
                ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
                ->load()
                ->toOptionHash();
            $this->addColumnAfter('set_name', array(
                'header'=> Mage::helper('catalog')->__('Attrib. Set Name'),
                'width' => '100px',
                'index' => 'attribute_set_id',
                'type'  => 'options',
                'options' => $sets,
            ), 'type');
            $this->addColumnAfter('brand', array(
                'header' => Mage::helper('catalog')->__('Brand'),
                'width' => '60px',
                'index' => 'brand',
                'type' => 'text',
            ), 'set_name');
        }
		
		$this->sortColumnsByOrder();

        return $this;
    }

    protected function _prepareCollection()
    {
        $isExport = $this->_isExport;
        $this->_isExport = TRUE;

		$store = $this->_getStore();
		
        parent::_prepareCollection();
        $collection = $this->getCollection();
        //$collection->addAttributeToSelect('best_seller');
		//$collection->addAttributeToSelect('ref_no');
		//$collection->addAttributeToSelect('special_price');
		//$collection->addAttributeToSelect('editors_pick');
		$collection->addAttributeToSelect('type_id');
		
        $attributes = array('best_seller','editors_pick','special_price', 'ref_no');
        foreach($attributes as $attribute_code){
            $tableName = $attribute_code.'_t';
            $attribute = $this->getAttribute($attribute_code);
            $attributeTable = $attribute->getBackend()->getTable();
            $attributeId = $attribute->getAttributeId();
            $collection->getSelect()
                ->joinLeft(
                    array($tableName => $attributeTable),
                    "e.entity_id={$tableName}.entity_id AND {$tableName}.store_id = '{$store->getId()}'"
                        . " AND {$tableName}.attribute_id='{$attributeId}'",
                    array( $attribute_code => 'value' ) // field as
                );
        }
		
        $this->_isExport = $isExport;

		$this->setCollection($collection);
		
        if (!$this->_isExport) {
            $this->getCollection()->load();
            $this->_afterLoadCollection();
        }
        return $this;
    }
	
    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }
	
    public function getAttribute($code)
    {
        if(!isset($this->_data[$code]) || is_null($this->_data[$code])){
            $this->_data[$code] = Mage::getSingleton('eav/config')->getAttribute('catalog_product', $code);
        }

        return $this->_data[$code];
    }
	
	public function _epFilter($collection ,$column)
	{
		if(!$value = $column->getFilter()->getValue())
		{
			return $this;
		}
		
		if(!is_null(trim($value)))
		{
			$this->getCollection()->addAttributeToFilter('editors_pick',array('like'=>"%$value%"));
		}
		
		return $this;
	}
	
	public function _epOrder($collection, $column)
	{
		$this->getCollection()->getSelect()->order('editors_pick '. strtoupper($column->getDir()));
	}
	
	public function _spFilter($collection ,$column)
	{
		if(!$value = $column->getFilter()->getValue())
		{
			return $this;
		}
		
		$from = $value['from'];
		$to = $value['to'];
		
		if(!is_null($from))
		{
			$this->getCollection()->addAttributeToFilter('special_price',array('gteq'=>$from));
		}
		
		if(!is_null($to))
		{
			$this->getCollection()->addAttributeToFilter('special_price',array('lteq'=>$to));
		}
		
		return $this;
	}
	
	public function _spOrder($collection, $column)
	{
		$this->getCollection()->getSelect()->order('special_price '. strtoupper($column->getDir()));
	}
	
	public function _bsFilter($collection ,$column)
	{
		if(!$value = $column->getFilter()->getValue())
		{
			return $this;
		}
		
		$from = $value['from'];
		$to = $value['to'];
		
		if(!is_null($from))
		{
			$this->getCollection()->addAttributeToFilter('best_seller',array('gteq'=>$from));
		}
		
		if(!is_null($to))
		{
			$this->getCollection()->addAttributeToFilter('best_seller',array('lteq'=>$to));
		}
		
		return $this;
	}
	
	public function _bsOrder($collection, $column)
	{
		$this->getCollection()->getSelect()->order('best_seller '. strtoupper($column->getDir()));
	}
	
	public function _rnFilter($collection ,$column)
	{
		if(!$value = $column->getFilter()->getValue())
		{
			return $this;
		}

		$this->getCollection()->addAttributeToFilter("ref_no",array('like'=>"%$value%"));
		
		return $this;
	}
	
	public function _rnOrder($collection, $column)
	{
		$this->getCollection()->getSelect()->order('ref_no '. strtoupper($column->getDir()));
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
