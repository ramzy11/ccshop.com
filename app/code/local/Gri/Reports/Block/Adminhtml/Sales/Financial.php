<?php

class Gri_Reports_Block_Adminhtml_Sales_Financial extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_blockGroup = 'gri_reports';
        $this->_controller = 'adminhtml_sales_financial';
        $this->_headerText = $this->__('Financial Report');
        parent::__construct();
        $this->_removeButton('add');
        $this->addButton('filter_form_submit', array(
            'label'     => Mage::helper('reports')->__('Show Report'),
            'onclick'   => 'filterFormSubmit()',
        ));
    }

    public function getFilterUrl()
    {
        $this->getRequest()->setParam('filter', NULL);
        return $this->getUrl('*/*/financial', array('_current' => TRUE));
    }
}
