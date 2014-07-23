<?php

include ("Mage/Adminhtml/controllers/IndexController.php");

class Gri_Ubl_Admin_IndexController extends  Mage_Adminhtml_IndexController
{
	
	public function changeLocaleAction()
    {
        $locale = $this->getRequest()->getParam('locale');
        if ($locale) {
            Mage::getSingleton('adminhtml/session')->setLocale($locale);
			//Added to capture user locale change
			Mage::dispatchEvent('gri_adminhtml_locale_change',array('locale'=>$locale,'userid'=>Mage::getSingleton('admin/session')->getUser()->getData('user_id')));
        }
        $this->_redirectReferer();
    }

}
