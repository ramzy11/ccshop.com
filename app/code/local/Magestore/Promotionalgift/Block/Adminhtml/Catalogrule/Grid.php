<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_Promotionalgift
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Promotionalgift Grid Block
 * 
 * @category    Magestore
 * @package     Magestore_Promotionalgift
 * @author      Magestore Developer
 */
class Magestore_Promotionalgift_Block_Adminhtml_Catalogrule_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('catalogruleGrid');
        $this->setDefaultSort('rule_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }
    
    /**
     * prepare collection for block to display
     *
     * @return Magestore_Promotionalgift_Block_Adminhtml_Promotionalgift_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('promotionalgift/catalogrule')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    /**
     * prepare columns for this grid
     *
     * @return Magestore_Promotionalgift_Block_Adminhtml_Promotionalgift_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('rule_id', array(
            'header'    => Mage::helper('promotionalgift')->__('ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'rule_id',
        ));

        $this->addColumn('name', array(
            'header'    => Mage::helper('promotionalgift')->__('Rule Name'),
            'align'     =>'left',
            'index'     => 'name',
        ));

        $this->addColumn('from_date', array(
            'header'    => Mage::helper('promotionalgift')->__('Date Start'),
            'align'     => 'left',
            'width'     => '120px',
            'type'      => 'date',
            'index'     => 'from_date',
        ));

        $this->addColumn('to_date', array(
            'header'    => Mage::helper('promotionalgift')->__('Date Expire'),
            'align'     => 'left',
            'width'     => '120px',
            'type'      => 'date',
            'default'   => '--',
            'index'     => 'to_date',
        ));

        $this->addColumn('status', array(
            'header'    => Mage::helper('promotionalgift')->__('Status'),
            'align'     => 'left',
            'width'     => '80px',
            'index'     => 'status',
            'type'        => 'options',
            'options'     => array(
                1 => 'Enabled',
                2 => 'Disabled',
            ),
        ));
		
		$this->addColumn('priority', array(
            'header'    => Mage::helper('promotionalgift')->__('Priority'),
            'align'     =>'left',
			'width'     => '80px',
            'index'     => 'priority',
        ));

        $this->addColumn('action',
            array(
                'header'    =>    Mage::helper('promotionalgift')->__('Action'),
                'width'        => '100',
                'type'        => 'action',
                'getter'    => 'getId',
                'actions'    => array(
                    array(
                        'caption'    => Mage::helper('promotionalgift')->__('Edit'),
                        'url'        => array('base'=> '*/*/edit'),
                        'field'        => 'id'
                    )),
                'filter'    => false,
                'sortable'    => false,
                'index'        => 'stores',
                'is_system'    => true,
        ));

        // $this->addExportType('*/*/exportCsv', Mage::helper('promotionalgift')->__('CSV'));
        // $this->addExportType('*/*/exportXml', Mage::helper('promotionalgift')->__('XML'));

        return parent::_prepareColumns();
    }
    
    /**
     * prepare mass action for this grid
     *
     * @return Magestore_Promotionalgift_Block_Adminhtml_Promotionalgift_Grid
     */
    // protected function _prepareMassaction()
    // {
        // $this->setMassactionIdField('promotionalgift_id');
        // $this->getMassactionBlock()->setFormFieldName('promotionalgift');

        // $this->getMassactionBlock()->addItem('delete', array(
            // 'label'        => Mage::helper('promotionalgift')->__('Delete'),
            // 'url'        => $this->getUrl('*/*/massDelete'),
            // 'confirm'    => Mage::helper('promotionalgift')->__('Are you sure?')
        // ));

        // $statuses = Mage::getSingleton('promotionalgift/status')->getOptionArray();

        // array_unshift($statuses, array('label'=>'', 'value'=>''));
        // $this->getMassactionBlock()->addItem('status', array(
            // 'label'=> Mage::helper('promotionalgift')->__('Change status'),
            // 'url'    => $this->getUrl('*/*/massStatus', array('_current'=>true)),
            // 'additional' => array(
                // 'visibility' => array(
                    // 'name'    => 'status',
                    // 'type'    => 'select',
                    // 'class'    => 'required-entry',
                    // 'label'    => Mage::helper('promotionalgift')->__('Status'),
                    // 'values'=> $statuses
                // ))
        // ));
        // return $this;
    // }
    
    /**
     * get url for each row in grid
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}