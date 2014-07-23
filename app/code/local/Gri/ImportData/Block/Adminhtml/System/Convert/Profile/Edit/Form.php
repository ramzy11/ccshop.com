<?php
class Gri_ImportData_Block_Adminhtml_System_Convert_Profile_Edit_Form extends Mage_Adminhtml_Block_System_Convert_Profile_Edit_Form
{
	protected function _prepareForm()
	{
		$form = parent::_prepareForm()->getForm();
		$form->setEnctype('multipart/form-data');
		$this->setForm($form);
		return $this;
	}

}