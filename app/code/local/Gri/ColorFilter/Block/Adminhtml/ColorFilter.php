<?php

class Gri_ColorFilter_Block_Adminhtml_ColorFilter extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    protected $_blockGroup = 'gri_colorfilter';
    protected $_controller = 'adminhtml_colorFilter';

    public function __construct()
    {
        $this->_headerText = Mage::helper('gri_colorfilter')->__('Manage Color Filter');
        $this->_addButtonLabel = Mage::helper('gri_colorfilter')->__('Add New Color');
        parent::__construct();
    }
}
