<?php

class Gri_Reports_Block_Adminhtml_Sales_Coupon_Summary_Form extends Mage_Adminhtml_Block_Report_Filter_Form
{

    protected function _prepareForm()
    {
        parent::_prepareForm();
        $form = $this->getForm();
        /* @var $fieldset Varien_Data_Form_Element_Fieldset */
        $fieldset = $form->getElement('base_fieldset');
        $this->_addElementTypes($fieldset);
        $fieldset->removeField('show_empty_rows')
            ->removeField('report_type')
            ->removeField('period_type')
            ->removeField('from')
            ->removeField('to');

        $dateFormat = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);

        $fieldset->addField('from', 'date', array(
            'name' => 'from',
            'format' => $dateFormat,
            'image' => $this->getSkinUrl('images/grid-cal.gif'),
            'label' => $this->__('Coupon Valid From'),
            'title' => $this->__('Coupon Valid From'),
        ));

        $fieldset->addField('to', 'date', array(
            'name' => 'to',
            'format' => $dateFormat,
            'image' => $this->getSkinUrl('images/grid-cal.gif'),
            'label' => $this->__('Coupon Valid To'),
            'title' => $this->__('Coupon Valid To'),
        ));

        $fieldset->addField('rule_id', 'text', array(
            'name' => 'rule_id',
            'label' => $this->__('Shopping Cart Price Rule ID'),
            'title' => $this->__('Shopping Cart Price Rule ID'),
        ));

        $fieldset->addField('rule_name', 'text', array(
            'name' => 'rule_name',
            'label' => $this->__('Shopping Cart Price Rule Name'),
            'title' => $this->__('Shopping Cart Price Rule Name'),
        ));

        $fieldset->addField('coupon_code', 'text', array(
            'name' => 'coupon_code',
            'label' => $this->__('Coupon Code'),
            'title' => $this->__('Coupon Code'),
        ));
        return $this;
    }
}
