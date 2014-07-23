<?php

class Gri_Reports_Block_Adminhtml_Sales_Summary_Form extends Mage_Adminhtml_Block_Report_Filter_Form
{

    protected function _getAdditionalElementTypes()
    {
        return array(
            'category_chooser' => Mage::getConfig()->getBlockClassName('gri_reports/adminhtml_form_element_categoryChooser'),
        );
    }

    protected function _prepareForm()
    {
        parent::_prepareForm();
        $form = $this->getForm();
        $htmlIdPrefix = $form->getHtmlIdPrefix();
        /* @var $fieldset Varien_Data_Form_Element_Fieldset */
        $fieldset = $form->getElement('base_fieldset');
        $this->_addElementTypes($fieldset);
        $fieldset->removeField('show_empty_rows');

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

        $fieldset->addField('output_type', 'select', array(
            'name' => 'output_type',
            'options' => array(
                'summary' => $this->__('Summary'),
                'group_by_category' => $this->__('Group by Category'),
                'group_by_style_no' => $this->__('Group by Style Number'),
            ),
            'label' => $this->__('Output Type'),
            'title' => $this->__('Output Type'),
        ));

        $fieldset->addField('categories', 'category_chooser', array(
            'name' => 'categories',
            'label' => $this->__('Categories'),
            'title' => $this->__('Categories'),
        ));
        /* @var $formAfter Mage_Core_Block_Text_List */
        if ($this->getFieldVisibility('output_type') &&
            $this->getFieldVisibility('categories') &&
            $formAfter = $this->getChild('form_after')
        ) {
            /* @var $formElementDependence Mage_Adminhtml_Block_Widget_Form_Element_Dependence */
            $formElementDependence = $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence', 'form_element_dependence');
            $formElementDependence->addFieldMap("{$htmlIdPrefix}output_type", 'output_type')
                ->addFieldMap("{$htmlIdPrefix}categories", 'categories')
                ->addConfigOptions(array('levels_up' => 5))
                ->addFieldDependence('categories', 'output_type', 'group_by_category');
            $formAfter->append($formElementDependence);
        }
        if ($this->getFieldVisibility('show_order_statuses') &&
            $this->getFieldVisibility('order_statuses') &&
            $formAfter
        ) {
            $formElementDependence = $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence', 'form_element_dependence_2');
            $formElementDependence->addFieldMap("{$htmlIdPrefix}show_order_statuses", 'show_order_statuses')
                ->addFieldMap("{$htmlIdPrefix}order_statuses", 'order_statuses')
                ->addFieldDependence('order_statuses', 'show_order_statuses', '1');
            $formAfter->append($formElementDependence);
        }
        return $this;
    }
}
