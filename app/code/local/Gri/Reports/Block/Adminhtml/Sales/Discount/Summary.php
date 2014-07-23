<?php

class Gri_Reports_Block_Adminhtml_Sales_Discount_Summary extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_blockGroup = 'gri_reports';
        $this->_controller = 'adminhtml_sales_discount_summary';
        $this->_headerText = $this->__('Discount % Distribution Report');
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
        return $this->getUrl('*/*/discountSummary', array('_current' => true));
    }
}
