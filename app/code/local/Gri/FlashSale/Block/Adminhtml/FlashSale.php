<?php

class Gri_FlashSale_Block_Adminhtml_FlashSale extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    protected $_blockGroup = 'gri_flashsale';
    protected $_controller = 'adminhtml_flashSale';

    public function __construct()
    {
        $this->_headerText = Mage::helper('gri_flashsale')->__('Manage Flash Sale');
        $this->_addButtonLabel = Mage::helper('gri_flashsale')->__('Add New Flash Sale');
        parent::__construct();
    }
}
