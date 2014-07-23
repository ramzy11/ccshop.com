<?php

class Gri_Customer_Block_Account_Forgotpassword extends Mage_Customer_Block_Account_Forgotpassword
{
    public function _prepareLayout()
    {
        /* @var $breadcrumbs Mage_Page_Block_Html_Breadcrumbs */
        $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
        $breadcrumbs and $breadcrumbs->addCrumb('home', array('label' => $this->__('Home'), 'title' =>$this->__('Home'),'link'=> Mage::getBaseUrl()));

        $breadcrumbs->addCrumb('forgotpassword', array('label' => $this->__('Forgot Password'), 'title' => $this->__('Forgot Password')));
        return parent::_prepareLayout();
    }
}
