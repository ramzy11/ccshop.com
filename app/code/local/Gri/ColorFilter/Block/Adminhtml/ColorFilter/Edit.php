<?php

class Gri_ColorFilter_Block_Adminhtml_ColorFilter_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    protected $_blockGroup = 'gri_colorfilter';
    protected $_controller = 'adminhtml_colorFilter';

    public function __construct()
    {
        parent::__construct();

        $this->_updateButton('save', 'label', Mage::helper('gri_colorfilter')->__('Save Color'));
        $this->_updateButton('delete', 'label', Mage::helper('gri_colorfilter')->__('Delete Color'));
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
        return Mage::registry('color_filter')->getId() ?
            Mage::helper('gri_colorfilter')->__('Edit Color') :
            Mage::helper('gri_colorfilter')->__('New Color');
    }

    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save');
    }
}
