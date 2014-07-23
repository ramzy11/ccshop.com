<?php

class Gri_Reports_Block_Adminhtml_Sales_Order_Detail_Form extends Mage_Adminhtml_Block_Report_Filter_Form
{

    protected function _prepareForm()
    {
        parent::_prepareForm();
        $form = $this->getForm();
        $htmlIdPrefix = $form->getHtmlIdPrefix();
        /* @var $fieldset Varien_Data_Form_Element_Fieldset */
        $fieldset = $form->getElement('base_fieldset');
        $this->_addElementTypes($fieldset);
        $fieldset->removeField('show_empty_rows');

        $fieldset->addField('increment_id', 'text', array(
            'name' => 'increment_id',
            'label' => $this->__('Order #'),
            'title' => $this->__('Order #'),
        ));

        $statuses = Mage::getModel('sales/order_config')->getStatuses();
        $values = array();
        foreach ($statuses as $code => $label) {
            if (false === strpos($code, 'pending')) {
                $values[] = array(
                    'label' => Mage::helper('reports')->__($label),
                    'value' => $code
                );
            }
        }

        $fieldset->addField('show_order_statuses', 'select', array(
            'name'      => 'show_order_statuses',
            'label'     => Mage::helper('reports')->__('Order Status'),
            'options'   => array(
                '0' => Mage::helper('reports')->__('Any'),
                '1' => Mage::helper('reports')->__('Specified'),
            ),
            'note'      => Mage::helper('reports')->__('Applies to Any of the Specified Order Statuses'),
        ), 'to');

        $fieldset->addField('order_statuses', 'multiselect', array(
            'name'      => 'order_statuses',
            'values'    => $values,
            'display'   => 'none'
        ), 'show_order_statuses');

        if ($this->getFieldVisibility('show_order_statuses') && $this->getFieldVisibility('order_statuses')) {
            $this->setChild('form_after', $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
                    ->addFieldMap("{$htmlIdPrefix}show_order_statuses", 'show_order_statuses')
                    ->addFieldMap("{$htmlIdPrefix}order_statuses", 'order_statuses')
                    ->addFieldDependence('order_statuses', 'show_order_statuses', '1')
            );
        }

        $fieldset->addField('email', 'text', array(
            'name' => 'email',
            'label' => $this->__('Customer Email'),
            'title' => $this->__('Customer Email'),
        ));

        $fieldset->addField('style_no', 'text', array(
            'name' => 'style_no',
            'label' => $this->__('Style Number'),
            'title' => $this->__('Style Number'),
        ));

        $fieldset->addField('style_name', 'text', array(
            'name' => 'style_name',
            'label' => $this->__('Style Name'),
            'title' => $this->__('Style Name'),
        ));

        return $this;
    }
}
