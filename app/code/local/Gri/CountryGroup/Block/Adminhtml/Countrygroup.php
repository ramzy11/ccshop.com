<?php
class Gri_CountryGroup_Block_Adminhtml_Countrygroup extends Mage_Adminhtml_Block_Widget_Container
{
	/**
	 * Set template
	 */
	public function __construct()
	{
		parent::__construct();
		$this->setTemplate('countrygroup/countrygroup.phtml');
	}

	protected function _prepareLayout()
	{
		$this->_addButton('add_new', array(
			'label'   => Mage::helper('catalog')->__('Add Country Group'),
			'onclick' => "setLocation('{$this->getUrl('*/*/new')}')",
			'class'   => 'add'
			));
		$this->setChild('grid', $this->getLayout()->createBlock('gri_countrygroup/adminhtml_grid', 'countrygroup.grid'));
		return parent::_prepareLayout();
	}

	public function getGridHtml()
	{
		return $this->getChildHtml('grid');
	}

	/**
	 * Check whether it is single store mode
	 *
	 * @return bool
	 */
	public function isSingleStoreMode()
	{
		if (!Mage::app()->isSingleStoreMode()) {
			return false;
		}
		return true;
	}
}