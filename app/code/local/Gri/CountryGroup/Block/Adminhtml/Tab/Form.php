<?php
class Gri_CountryGroup_Block_Adminhtml_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
	public function getCountryGroup()
	{
		$id = (int)($this->getRequest()->getParam('id'));
		if (!($this->getData('countrygroup') instanceof Gri_CountryGroup_Model_CountryGroup)) {
			$this->setData('countrygroup', Mage::getModel('gri_countrygroup/countrygroup')->load($id));
		}
		return $this->getData('countrygroup');
	}

	public function getAllCountryData()
	{
		if(!($this->getData('countrydata')))
			$this->setData('countrydata',Mage::getResourceModel('directory/country_collection')->loadData()->toOptionArray(false));
		return $this->getData('countrydata');

	}
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form();
		$this->setForm($form);
		$fieldset = $form->addFieldset('countrygroup_form', array('legend'=>Mage::helper('gri_countrygroup')->__('Country Group information')));
		$fieldset->addField('editor', 'text', array(
			'label' => Mage::helper('gri_countrygroup')->__('Country Group'),
			'class' => 'required-entry',
			'note'  => Mage::helper('gri_countrygroup')->__('For internal use, Must be unique'),
			'required' => true,
			'name' => 'name',
			'value' =>$this->getCountryGroup()->getData('name'),
		));

		$fieldset->addField('country','multiselect',array(
			'label' => Mage::helper('gri_countrygroup')->__('Allow Countries'),
			'values'=> $this->getAllCountryData(),
			'value' => $this->getCountryGroup()->getValue(),
			'name'  =>'countries'
			));
		return parent::_prepareForm();
	}
}