<?php

class Magestore_Promotionalgift_Block_Adminhtml_Catalogrule_Edit_Tab_Conditions extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      if ( Mage::getSingleton('adminhtml/session')->getFormData()){
          $data = Mage::getSingleton('adminhtml/session')->getFormData();
          $model = Mage::getModel('promotionalgift/catalogrule')
          		->load($data['rule_id'])
		  		->setData($data);
          Mage::getSingleton('adminhtml/session')->setFormData(null);
      } elseif ( Mage::registry('catalogrule_data')){
          $model = Mage::registry('catalogrule_data');
          $data = $model->getData();
      }
  	  
      $form = new Varien_Data_Form();
      $form->setHtmlIdPrefix('rule_');
      
      $renderer = Mage::getBlockSingleton('adminhtml/widget_form_renderer_fieldset')
            ->setTemplate('promo/fieldset.phtml')
            ->setNewChildUrl($this->getUrl('adminhtml/promo_quote/newConditionHtml/form/rule_conditions_fieldset'));
      
      $fieldset = $form->addFieldset('conditions_fieldset', array('legend'=>Mage::helper('promotionalgift')->__('Apply the rule only if the following conditions are met (leave blank for all orders)')))->setRenderer($renderer);
      
      $fieldset->addField('conditions','text',array(
      	'name'	=> 'conditions',
      	'label'	=> Mage::helper('promotionalgift')->__('Conditions'),
      	'title'	=> Mage::helper('promotionalgift')->__('Conditions'),
      	'required'	=> true,
	  ))->setRule($model)->setRenderer(Mage::getBlockSingleton('rule/conditions'));
      
      $form->setValues($data);
      $this->setForm($form);
      return parent::_prepareForm();
  }
}