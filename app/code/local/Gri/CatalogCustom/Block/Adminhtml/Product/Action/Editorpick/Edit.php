<?php
class Gri_CatalogCustom_Block_Adminhtml_Product_Action_Editorpick_Edit extends Mage_Adminhtml_Block_Catalog_Product_Edit_Action_Attribute
{

	protected function _prepareLayout()
	{
		$this->setChild('back_button',
			$this->getLayout()->createBlock('adminhtml/widget_button')
			->setData(array(
				'label'     => Mage::helper('catalog')->__('Back'),
				'onclick'   => 'setLocation(\''.$this->getUrl('*/catalogcustom_editorpick/', array('store'=>$this->getRequest()->getParam('store', 0))).'\')',
				'class' => 'back'
			))
		);

		$this->setChild('reset_button',
			$this->getLayout()->createBlock('adminhtml/widget_button')
			->setData(array(
				'label'     => Mage::helper('catalog')->__('Reset'),
				'onclick'   => 'setLocation(\''.$this->getUrl('*/*/*', array('_current'=>true)).'\')'
			))
		);

		$this->setChild('save_button',
			$this->getLayout()->createBlock('adminhtml/widget_button')
			->setData(array(
				'label'     => Mage::helper('catalog')->__('Save'),
				'onclick'   => 'attributesForm.submit()',
				'class'     => 'save'
			))
		);
	}
}