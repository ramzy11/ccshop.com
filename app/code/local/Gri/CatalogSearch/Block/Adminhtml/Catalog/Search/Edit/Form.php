<?php
class Gri_CatalogSearch_Block_Adminhtml_Catalog_Search_Edit_Form extends Mage_Adminhtml_Block_Catalog_Search_Edit_Form
{
	public function _prepareForm()
	{
		$form = parent::_prepareForm()->getForm();
		$fieldset = $form->getElement('base_fieldset');
		$fieldset->addField('promoted_terms', 'text', array(
	            'name'      => 'promoted_terms',
	            'label'     => Mage::helper('catalog')->__('Promoted Terms'),
	            'title'     => Mage::helper('catalog')->__('Promoted Terms'),
	            'required'  => False,
		  		'note'  => Mage::helper('catalog')->__('Separate terms with comma.'),
	        ),'synonym_for');
		$form->setValues(Mage::registry('current_catalog_search')->getData());
		$this->setForm($form);
		return $this;
	}
}