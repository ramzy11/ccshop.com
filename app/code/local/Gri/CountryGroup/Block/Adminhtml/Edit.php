<?php
class Gri_CountryGroup_Block_Adminhtml_Edit extends Mage_Adminhtml_Block_Widget
{

	public function __construct()
	{
		parent::__construct();
		$this->setTemplate('countrygroup/edit.phtml');
		$this->setId('countrygroup_edit');
	}


	public function getCountryGroup()
	{
		$id = (int)($this->getRequest()->getParam('id'));
		if (!($this->getData('countrygroup') instanceof Gri_CountryGroup_Model_CountryGroup)) {
			$this->setData('countrygroup', Mage::getModel('gri_countrygroup/countrygroup')->load($id));
		}
		return $this->getData('countrygroup');
	}

	protected function _prepareLayout()
	{
		if (!$this->getRequest()->getParam('popup')) {
			$this->setChild('back_button',
				$this->getLayout()->createBlock('adminhtml/widget_button')
				->setData(array(
					'label'     => Mage::helper('catalog')->__('Back'),
					'onclick'   => 'setLocation(\''.$this->getUrl('*/*/', array('store'=>$this->getRequest()->getParam('store', 0))).'\')',
					'class' => 'back'
				))
			);
		} else {
			$this->setChild('back_button',
				$this->getLayout()->createBlock('adminhtml/widget_button')
				->setData(array(
					'label'     => Mage::helper('catalog')->__('Close Window'),
					'onclick'   => 'window.close()',
					'class' => 'cancel'
				))
			);
		}

		$this->setChild('save_button',
			$this->getLayout()->createBlock('adminhtml/widget_button')
			->setData(array(
				'label'     => Mage::helper('gri_catalogcustom')->__('Save'),
				'onclick'   => 'countrygroupForm.submit()',
				'class' => 'save'
			))
		);

		if (!$this->getRequest()->getParam('popup')) {
			$this->setChild('save_and_edit_button',
				$this->getLayout()->createBlock('adminhtml/widget_button')
				->setData(array(
					'label'     => Mage::helper('gri_catalogcustom')->__('Save and Continue Edit'),
					'onclick'   => 'saveAndContinueEdit(\''.$this->getSaveAndContinueUrl().'\')',
					'class' => 'save'
				))
			);

		}
		if ($this->getCountryGroup()->isDeleteable()) {
			$this->setChild('delete_button',
				$this->getLayout()->createBlock('adminhtml/widget_button')
				->setData(array(
					'label'     => Mage::helper('gri_countrygroup')->__('Delete'),
					'onclick'   => 'confirmSetLocation(\''.Mage::helper('gri_countrygroup')->__('Are you sure?').'\', \''.$this->getDeleteUrl().'\')',
					'class'  => 'delete'
				))
			);
		}

		return parent::_prepareLayout();
	}

	public function getBackButtonHtml()
	{
		return $this->getChildHtml('back_button');
	}

	public function getCancelButtonHtml()
	{
		return $this->getChildHtml('reset_button');
	}

	public function getSaveButtonHtml()
	{
		return $this->getChildHtml('save_button');
	}

	public function getSaveAndEditButtonHtml()
	{
		return $this->getChildHtml('save_and_edit_button');
	}

	public function getDeleteButtonHtml()
	{
		return $this->getChildHtml('delete_button');
	}

	public function getValidationUrl()
	{
		return $this->getUrl('*/*/validate', array('_current'=>true));
	}

	public function getSaveUrl()
	{
		return $this->getUrl('*/*/save', array('_current'=>true, 'back'=>null));
	}

	public function getSaveAndContinueUrl()
	{
		return $this->getUrl('*/*/save', array(
			'_current'   => true,
			'back'       => 'edit',
			'tab'        => '{{tab_id}}',
			'active_tab' => null
		));
	}

	public function getCountryGroupId()
	{
		return $this->getCountryGroup()->getId();
	}

	public function getDeleteUrl()
	{
		return $this->getUrl('*/*/delete', array('_current'=>true));
	}

	public function getHeader()
	{
		$header = '';
		if ($this->getCountryGroupId()) {
			$header = $this->htmlEscape($this->getCountryGroup()->getName());
		}
		else {
			$header = Mage::helper('gri_countrygroup')->__('New Country Group');
		}
		return $header;
	}

	public function getSelectedTabId()
	{
		return addslashes(htmlspecialchars($this->getRequest()->getParam('tab')));
	}

}