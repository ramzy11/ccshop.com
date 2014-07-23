<?php
class Gri_CountryGroup_Block_Adminhtml_Grid extends Mage_Adminhtml_Block_Widget_Grid{

	public function __construct() {
		parent::__construct ();
		$this->setId ( 'countrygroupGrid' );
		$this->setDefaultSort ('country_group_id' );
		$this->setDefaultDir ( 'DESC' );
		$this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setVarNameFilter('country_filter');
	}

 protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }

    protected function _prepareCollection()
    {
        $store = $this->_getStore();
        $collection = Mage::getModel('gri_countrygroup/countrygroup')->getCollection();
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    protected function _prepareColumns()
    {
        $this->addColumn('country_group_id',
            array(
                'header'=> Mage::helper('gri_countrygroup')->__('ID'),
                'type'  => 'number',
                'index' => 'country_group_id',
        ));
        $this->addColumn('name',
            array(
                'header'=> Mage::helper('gri_countrygroup')->__('Country Group Name'),
                'index' => 'name',
        ));

        $this->addColumn('value',
            array(
                'header'=> Mage::helper('gri_countrygroup')->__('Countries'),
                'index' => 'value',
        ));
        $store = $this->_getStore();
        $this->addColumn('action',
            array(
                'header'    => Mage::helper('gri_countrygroup')->__('Action'),
                'width'     => '50px',
                'type'      => 'action',
                'getter'     => 'getId',
                'actions'   => array(
                    array(
                        'caption' => Mage::helper('gri_countrygroup')->__('Edit'),
                        'url'     => array(
                            'base'=>'*/*/edit',
                            'params'=>array('store'=>$this->getRequest()->getParam('store'))
                        ),
                        'field'   => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
        ));

        if (Mage::helper('gri_countrygroup')->isModuleEnabled('Mage_Rss')) {
            $this->addRssList('rss/catalog/notifystock', Mage::helper('gri_countrygroup')->__('Notify Low Stock RSS'));
        }

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('countrygroup');

        $this->getMassactionBlock()->addItem('delete', array(
        	'label'=> Mage::helper('catalog')->__('Delete'),
        	'url'  => $this->getUrl('*/*/massDelete'),
        	'confirm' => Mage::helper('catalog')->__('Are you sure?')
        ));
       // Mage::dispatchEvent('adminhtml_catalog_product_grid_prepare_massaction', array('block' => $this));
        return $this;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array(
            'store'=>$this->getRequest()->getParam('store'),
            'id'=>$row->getId())
        );
    }
}
