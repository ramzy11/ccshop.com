<?php

class Gri_Reports_Block_Adminhtml_Sales_Rma_Details extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_blockGroup = 'gri_reports';
        $this->_controller = 'adminhtml_sales_rma_details';
        $this->_headerText = $this->__('Returns & Exchanges Report');
        parent::__construct();
        $this->_removeButton('add');
        $this->addButton('filter_form_submit', array(
            'label'     => Mage::helper('reports')->__('Show Report'),
            'onclick'   => 'filterFormSubmit()'
        ));
    }

    public function getFilterUrl()
    {
        $this->getRequest()->setParam('filter', null);
        return $this->getUrl('*/*/rmaDetails', array('_current' => true));
    }
}
