<?php
class Gri_ImportData_Block_Adminhtml_System_Convert_Profile_Imageprocess extends Mage_Adminhtml_Block_Abstract
{
	public function getTotalImage(){
		return Mage::registry('totalImage');
	}
	public function getFailedImage(){
		return Mage::registry('failedImage');
	}
}