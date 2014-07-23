<?php

class Gri_Customer_Block_Address_Book extends Mage_Customer_Block_Address_Book
{
    protected function _prepareLayout()
    {
        $this->getLayout()->getBlock('head')->setTitle(Mage::helper('customer')->__('Address Book'));
        /* @var $breadcrumbs Mage_Page_Block_Html_Breadcrumbs */
        $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
        $breadcrumbs and $breadcrumbs->addCrumb('home', array('label' => $this->__('Home'), 'title' => $this->__('Home'), 'link' => Mage::getBaseUrl()));
        $breadcrumbs->addCrumb('my-account', array('label' => $this->__('My Account'), 'title' => $this->__('My Account')));
        $breadcrumbs->addCrumb('address-book', array('label' => $this->__('Address Book'), 'title' => $this->__('Address Book')));
        return parent::_prepareLayout();
    }
}