<?php

class Gri_Reports_Block_Adminhtml_Sales_Financial_Form extends Mage_Adminhtml_Block_Report_Filter_Form
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
        $fieldset->removeField('period_type');

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

        $showStatus = $this->getFilterData('from') === NULL ? 1 : $this->getFilterData('show_order_statuses');
        $statuses = $this->getFilterData('from') === NULL ?
            array('complete', 'refunded', 'partial_refunded') : $this->getFilterData('order_statuses');

        $fieldset->addField('show_order_statuses', 'select', array(
            'name'      => 'show_order_statuses',
            'label'     => Mage::helper('reports')->__('Order Status'),
            'options'   => array(
                '0' => Mage::helper('reports')->__('Any'),
                '1' => Mage::helper('reports')->__('Specified'),
            ),
            'note'      => Mage::helper('reports')->__('Applies to Any of the Specified Order Statuses'),
            'value' => $showStatus,
        ), 'to');

        $fieldset->addField('order_statuses', 'multiselect', array(
            'name'      => 'order_statuses',
            'values'    => $values,
            'display'   => 'none',
            'value' => $statuses,
        ), 'show_order_statuses');
        /* @var $formAfter Mage_Core_Block_Text_List */
        $formAfter = $this->getChild('form_after');
        if ($this->getFieldVisibility('show_order_statuses') &&
            $this->getFieldVisibility('order_statuses') &&
            $formAfter
        ) {
            $formElementDependence = $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence', 'form_element_dependence');
            $formElementDependence->addFieldMap("{$htmlIdPrefix}show_order_statuses", 'show_order_statuses')
                ->addFieldMap("{$htmlIdPrefix}order_statuses", 'order_statuses')
                ->addFieldDependence('order_statuses', 'show_order_statuses', '1');
            $formAfter->append($formElementDependence);
        }

        $fieldset->addField('txn_id', 'text', array(
            'name' => 'txn_id',
            'label' => $this->__('Transaction ID'),
            'title' => $this->__('Transaction ID'),
        ));

        $fieldset->addField('order_id', 'text', array(
            'name' => 'order_id',
            'label' => $this->__('Order ID'),
            'title' => $this->__('Order ID'),
        ));

        $fieldset->addField('recipient', 'text', array(
            'name' => 'recipient',
            'label' => $this->__('Ship to Name'),
            'title' => $this->__('Ship to Name'),
        ));
        return $this;
    }
}
