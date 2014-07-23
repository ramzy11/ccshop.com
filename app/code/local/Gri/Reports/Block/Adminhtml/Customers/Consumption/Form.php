<?php

class Gri_Reports_Block_Adminhtml_Customers_Consumption_Form extends Mage_Adminhtml_Block_Report_Filter_Form
{

    protected function _prepareForm()
    {
        parent::_prepareForm();
        $form = $this->getForm();
        /* @var $fieldset Varien_Data_Form_Element_Fieldset */
        $fieldset = $form->getElement('base_fieldset');
        $this->_addElementTypes($fieldset);

        $fieldset->removeField('show_empty_rows');

        $fieldset->addField('email', 'text', array(
            'name' => 'email',
            'label' => $this->__('Customer Email'),
            'title' => $this->__('Customer Email'),
        ));

        return $this;
    }
}
