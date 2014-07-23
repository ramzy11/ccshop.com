<?php
class Gri_CatalogCustom_Block_Adminhtml_Catalogcustom_Sizemap_Grid extends Mage_Adminhtml_Block_Widget_Grid{

	public function __construct() {
		parent::__construct ();
		$this->setId ( 'productGrid' );
		$this->setDefaultSort ('universal_size' );
		$this->setDefaultDir ( 'DESC' );
		$this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setVarNameFilter('product_filter');
	}

 protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }

    protected function _prepareCollection()
    {
        $store = $this->_getStore();
        $collection = Mage::getModel('gri_catalogcustom/sizemap')->getCollection();
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    protected function _prepareColumns()
    {
        $this->addColumn('mapping_id',
            array(
                'header'=> Mage::helper('gri_catalogcustom')->__('ID'),
                'type'  => 'number',
                'index' => 'mapping_id',
        ));
        $this->addColumn('admin_size',
            array(
                'header'=> Mage::helper('gri_catalogcustom')->__('Actual Size'),
                'index' => 'admin_size',
        ));

        $this->addColumn('universal_size',
            array(
                'header'=> Mage::helper('gri_catalogcustom')->__('Map Size'),
                'index' => 'universal_size',
        ));
        $store = $this->_getStore();
        $this->addColumn('action',
            array(
                'header'    => Mage::helper('gri_catalogcustom')->__('Action'),
                'width'     => '50px',
                'type'      => 'action',
                'getter'     => 'getId',
                'actions'   => array(
                    array(
                        'caption' => Mage::helper('gri_catalogcustom')->__('Edit'),
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

        if (Mage::helper('gri_catalogcustom')->isModuleEnabled('Mage_Rss')) {
            $this->addRssList('rss/catalog/notifystock', Mage::helper('gri_catalogcustom')->__('Notify Low Stock RSS'));
        }

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('product');
        if (Mage::getSingleton('admin/session')->isAllowed('catalog/update_attributes')){
            $this->getMassactionBlock()->addItem('attributes', array(
                'label' => Mage::helper('catalog')->__('Update Attributes'),
                'url'   => $this->getUrl('*/catalogcustom_product_action_editorpick/edit', array('_current'=>true))
            ));
        }

        Mage::dispatchEvent('adminhtml_catalog_product_grid_prepare_massaction', array('block' => $this));
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