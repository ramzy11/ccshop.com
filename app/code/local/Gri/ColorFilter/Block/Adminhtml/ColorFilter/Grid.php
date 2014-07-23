<?php

class Gri_ColorFilter_Block_Adminhtml_ColorFilter_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    protected function _construct()
    {
        parent::_construct();
        $this->setId('colorFilterGrid');
        $this->setDefaultSort('color_id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(1);
        //$this->setVarNameFilter('flash_sale_filter');
    }

    protected function _prepareColumns()
    {
        $helper = $this->helper('gri_colorfilter');
        $this->addColumn('color_id', array(
            'header' => $helper->__('ID'),
            'index' => 'color_id',
            'type' => 'number',
        ));
        $this->addColumn('label', array(
            'header' => $helper->__('Label'),
            'index' => 'label',
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
        $this->setMassactionIdField('color_id');
        $this->getMassactionBlock()->setFormFieldName('color_filter');
        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('adminhtml')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete', array('_current' => true))
        ));
        return $this;
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('gri_colorfilter/colorFilter')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
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
