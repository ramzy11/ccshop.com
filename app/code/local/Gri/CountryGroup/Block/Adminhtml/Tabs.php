<?php

class Gri_CountryGroup_Block_Adminhtml_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	protected $_attributeTabBlock = 'adminhtml/catalog_product_edit_tab_attributes';

	public function __construct()
	{
		parent::__construct();
		$this->setId('countrygroup_info_tabs');
		$this->setDestElementId('countrygroup_edit_form');
		$this->setTitle(Mage::helper('catalog')->__('Country Group Information'));
	}
	protected function _beforeToHtml()
	{
		$this->addTab('form_section', array(
			'label'     => Mage::helper('gri_countrygroup')->__('Item Information'),
			'title'     => Mage::helper('gri_countrygroup')->__('Item Information'),
			'content'   => $this->getLayout()->createBlock('gri_countrygroup/adminhtml_tab_form')->toHtml(),
		));
		return parent::_beforeToHtml();
	}
}
