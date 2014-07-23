<?php

class Gri_CatalogCustom_Block_Adminhtml_Sizemap_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
	public function getSizemap()
	{
		$id = (int)($this->getRequest()->getParam('id'));
        if (!($this->getData('sizemap') instanceof Gri_Catalog_Model_SizeMap)) {
			$this->setData('sizemap', Mage::getModel('gri_catalogcustom/sizemap')->load($id));
		}
		return $this->getData('sizemap');
	}
  protected function _prepareForm()
  {

      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('sizemap_form', array('legend'=>Mage::helper('gri_catalogcustom')->__('Size Map information')));
      $fieldset->addField('editor', 'text', array(
			'label' => Mage::helper('gri_catalogcustom')->__('Universal Size'),
			'class' => 'required-entry',
			'required' => true,
			'name' => 'universal size',
			'value' =>$this->getSizemap()->getData('universal_size'),
      ));
     /*
      if ( Mage::getSingleton('adminhtml/session')->getNewsData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getNewsData());
          Mage::getSingleton('adminhtml/session')->setNewsData(null);
      } elseif ( Mage::registry('news_data') ) {
          $form->setValues(Mage::registry('news_data')->getData());
      }
     */
      return parent::_prepareForm();
  }
}