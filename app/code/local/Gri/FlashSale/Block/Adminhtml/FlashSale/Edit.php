<?php

class Gri_FlashSale_Block_Adminhtml_Flashsale_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    protected $_blockGroup = 'gri_flashsale';
    protected $_controller = 'adminhtml_flashSale';

    public function __construct()
    {
        parent::__construct();

        $this->_updateButton('save', 'label', Mage::helper('gri_flashsale')->__('Save Flash Sale'));
        $this->_updateButton('delete', 'label', Mage::helper('gri_flashsale')->__('Delete Flash Sale'));
        $this->addButton('save_and_edit_button', array(
            'label' => Mage::helper('adminhtml')->__('Save and Continue Edit'),
            'onclick'   => '$(\'back\').value = \'edit\';editForm.submit();',
            'class' => 'save',
        ), 1);
    }

    /**
     * Get Header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        return Mage::registry('flash_sale')->getId() ?
            Mage::helper('gri_flashsale')->__('Edit Flash Sale') :
            Mage::helper('gri_flashsale')->__('New Flash Sale');
    }

    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save');
    }
}
