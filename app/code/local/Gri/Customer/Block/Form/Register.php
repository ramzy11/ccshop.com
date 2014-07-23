<?php

class Gri_Customer_Block_Form_Register extends Mage_Customer_Block_Form_Register
{
    protected function _prepareLayout()
    {
        $this->getLayout()->getBlock('head')->setTitle(Mage::helper('customer')->__('Create New Customer Account'));
        /* @var $breadcrumbs Mage_Page_Block_Html_Breadcrumbs */
        $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
        $breadcrumbs and $breadcrumbs->addCrumb('home', array('label' => $this->__('Home'), 'title' => $this->__('Home'), 'link' => Mage::getBaseUrl()));

        $breadcrumbs->addCrumb('create-an-account', array('label' => $this->__('Create an Account'), 'title' => $this->__('Create an Account')));
        return parent::_prepareLayout();
    }
}
