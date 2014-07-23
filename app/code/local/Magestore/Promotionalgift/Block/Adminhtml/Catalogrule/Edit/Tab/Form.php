<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_Promotionalgift
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Promotionalgift Edit Form Content Tab Block
 * 
 * @category    Magestore
 * @package     Magestore_Promotionalgift
 * @author      Magestore Developer
 */
class Magestore_Promotionalgift_Block_Adminhtml_Catalogrule_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * prepare tab form's information
     *
     * @return Magestore_Promotionalgift_Block_Adminhtml_Promotionalgift_Edit_Tab_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        
        if (Mage::getSingleton('adminhtml/session')->getCatalogruleData()) {
            $data = Mage::getSingleton('adminhtml/session')->getCatalogruleData();
            Mage::getSingleton('adminhtml/session')->setCatalogruleData(null);
        } elseif (Mage::registry('catalogrule_data')) {
            $data = Mage::registry('catalogrule_data')->getData();
        }
        $fieldset = $form->addFieldset('catalogrule_form', array(
            'legend'=>Mage::helper('promotionalgift')->__('Rule information')
        ));
		
		$inStore = $this->getRequest()->getParam('store');
		$defaultLabel = Mage::helper('promotionalgift')->__('Use Default');
		$defaultTitle = Mage::helper('promotionalgift')->__('-- Please Select --');
		$scopeLabel = Mage::helper('promotionalgift')->__('STORE VIEW');
		
        $fieldset->addField('name', 'text', array(
            'label'        => Mage::helper('promotionalgift')->__('Rule Name'),
            'class'        => 'required-entry',
            'required'    => true,
            'name'        => 'name',
        ));

        $fieldset->addField('description', 'textarea', array(
            'name'       => 'description',
            'label'      => Mage::helper('promotionalgift')->__('Description'),
            'title'      => Mage::helper('promotionalgift')->__('Description'),
            'style'		 => 'width: 98%; height: 100px;',
            'wysiwyg'    => false,
            'required'   => true,
        ));
		
        $fieldset->addField('status', 'select', array(
            'label'        => Mage::helper('promotionalgift')->__('Status'),
            'name'        => 'status',
            'values'    => Mage::getSingleton('promotionalgift/status')->getOptionHash(),
        ));
		
		if (!Mage::app()->isSingleStoreMode()) {
            $fieldset->addField('website_ids', 'multiselect', array(
                'name'      => 'website_ids[]',
                'label'     => Mage::helper('promotionalgift')->__('Websites'),
                'title'     => Mage::helper('promotionalgift')->__('Websites'),
                'required'  => true,
                'values'    => Mage::getSingleton('adminhtml/system_config_source_website')->toOptionArray(),
            ));
        }
        else {
            $fieldset->addField('website_ids', 'hidden', array(
                'name'      => 'website_ids[]',
                'value'     => Mage::app()->getStore(true)->getWebsiteId()
            ));
            $data['website_ids']= Mage::app()->getStore(true)->getWebsiteId();
        }

		$customerGroups = Mage::getResourceModel('customer/group_collection')
            ->load()->toOptionArray();

        $found = false;
        foreach ($customerGroups as $group) {
            if ($group['value']==0) {
                $found = true;
            }
        }
        if (!$found) {
            array_unshift($customerGroups, array('value'=>0, 'label'=>Mage::helper('promotionalgift')->__('NOT LOGGED IN')));
        }

        $fieldset->addField('customer_group_ids', 'multiselect', array(
            'name'      => 'customer_group_ids[]',
            'label'     => Mage::helper('promotionalgift')->__('Customer Groups'),
            'title'     => Mage::helper('promotionalgift')->__('Customer Groups'),
            'required'  => true,
            'values'    => $customerGroups,
        ));
		$fieldset->addField('uses_limit', 'text', array(
            'label'        => Mage::helper('promotionalgift')->__('Use Limit'),
            //'class'        => 'required-entry',
            //'required'    => true,
            'name'        => 'uses_limit',
			'note'      => Mage::helper('promotionalgift')->__('The number of times the rule is applied. Leave blank for unlimited.'),
        ));
		/*
		$fieldset->addField('time_used', 'text', array(
            'label'        => Mage::helper('promotionalgift')->__('Time Used'),
            //'class'        => 'required-entry',
            //'required'    => true,
            'name'        => 'time_used',
			'note'        => Mage::helper('promotionalgift')->__('The time that customer can use this promotion. Empty is unlimited.'),
        ));
		*/
		$dateFormatIso = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
        $fieldset->addField('from_date', 'date', array(
            'name'   => 'from_date',
            'label'  => Mage::helper('promotionalgift')->__('Start Date'),
            'title'  => Mage::helper('promotionalgift')->__('Start Date'),
            'image'  => $this->getSkinUrl('images/grid-cal.gif'),
            'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
            'format'       => $dateFormatIso
        ));
        $fieldset->addField('to_date', 'date', array(
            'name'   => 'to_date',
            'label'  => Mage::helper('promotionalgift')->__('End Date'),
            'title'  => Mage::helper('promotionalgift')->__('End Date'),
            'image'  => $this->getSkinUrl('images/grid-cal.gif'),
            'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
            'format'       => $dateFormatIso
        ));

        $fieldset->addField('priority', 'text', array(
            'name' => 'priority',
            'label' => Mage::helper('promotionalgift')->__('Priority'),
			'note'  => Mage::helper('promotionalgift')->__('The smaller the value, the higher the priority.'),
        ));
		
        $form->setValues($data);
        return parent::_prepareForm();
    }
}