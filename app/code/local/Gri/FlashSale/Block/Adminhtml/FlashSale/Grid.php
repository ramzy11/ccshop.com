<?php

class Gri_FlashSale_Block_Adminhtml_FlashSale_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    protected function _construct()
    {
        parent::_construct();
        $this->setId('flashSaleGrid');
        $this->setDefaultSort('flash_sale_id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(1);
        $this->setVarNameFilter('flash_sale_filter');
    }

    protected function _prepareColumns()
    {
        $helper = $this->helper('gri_flashsale');
        $this->addColumn('flash_sale_id', array(
            'header' => $helper->__('ID'),
            'index' => 'flash_sale_id',
            'type' => 'number',
        ));
        $this->addColumn('title', array(
            'header' => $helper->__('Title'),
            'index' => 'title',
        ));
        $this->addColumn('flash_sale_id', array(
            'header' => $helper->__('ID'),
            'type' => 'number',
            'index' => 'flash_sale_id',
        ));
        $this->addColumn('start', array(
            'header' => $helper->__('Start Time'),
            'index' => 'start',
            'type' => 'datetime',
            'width' => 150,
        ));
        $this->addColumn('end', array(
            'header' => $helper->__('End Time'),
            'index' => 'end',
            'type' => 'datetime',
            'width' => 150,
        ));
        $this->addColumn('created_at', array(
            'header' => $helper->__('Created At'),
            'index' => 'created_at',
            'type' => 'datetime',
            'width' => 150,
        ));
        $this->addColumn('updated_at', array(
            'header' => $helper->__('Updated At'),
            'index' => 'updated_at',
            'type' => 'datetime',
            'width' => 150,
        ));
        $this->addColumn('is_active', array(
            'header' => $helper->__('Is Active'),
            'index' => 'is_active',
            'type' => 'options',
            'options' => Mage::getSingleton('adminhtml/system_config_source_yesno')->toArray(),
            'width' => 50,
        ));

        $this->addColumn('action', array(
            'header' => $helper->__('Action'),
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => $helper->__('Edit'),
                    'url' => array(
                        'base' => '*/*/edit',
                    ),
                    'field' => 'id'
                ),
                array(
                    'caption' => $helper->__('Activate'),
                    'url' => array(
                        'base' => '*/*/activate',
                    ),
                    'field' => 'id'
                ),
            ),
            'filter' => FALSE,
            'sortable' => FALSE,
            'width' => 80,
        ));
        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('flash_sale_id');
        $this->getMassactionBlock()->setFormFieldName('flash_sale');
        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('adminhtml')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete', array('_current' => true))
        ));
        return $this;
    }

    public function getCollection()
    {
        if (!$this->_collection) {
            $this->_collection = Mage::getResourceModel('gri_flashsale/flashSale_collection');
        }
        return $this->_collection;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array(
                'id' => $row->getId())
        );
    }
}
