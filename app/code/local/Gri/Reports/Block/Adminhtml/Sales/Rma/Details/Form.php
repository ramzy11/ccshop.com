<?php

class Gri_Reports_Block_Adminhtml_Sales_Rma_Details_Form extends Mage_Adminhtml_Block_Report_Filter_Form
{

    protected function _prepareForm()
    {
        parent::_prepareForm();
        $form = $this->getForm();
        /* @var $fieldset Varien_Data_Form_Element_Fieldset */
        $fieldset = $form->getElement('base_fieldset');
        $this->_addElementTypes($fieldset);
        $fieldset->removeField('show_empty_rows');
        $fieldset->addField('rma_no', 'text', array(
            'name' => 'rma_no',
            'label' => $this->__('RMA #'),
            'title' => $this->__('RMA #'),
        ));
        $fieldset->addField('order_no', 'text', array(
            'name' => 'order_no',
            'label' => $this->__('Order #'),
            'title' => $this->__('Order #'),
        ));
        $fieldset->addField('email', 'text', array(
            'name' => 'email',
            'label' => $this->__('Customer Email'),
            'title' => $this->__('Customer Email'),
        ));
        return $this;
    }
}
