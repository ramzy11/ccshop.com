<?php

class Gri_Reports_Block_Adminhtml_Sales_Product_Summary extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_blockGroup = 'gri_reports';
        $this->_controller = 'adminhtml_sales_product_summary';
        $this->_headerText = $this->__('Product Sales Rank');
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
        return $this->getUrl('*/*/productSummary', array('_current' => true));
    }
}
