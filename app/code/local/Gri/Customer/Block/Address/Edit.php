<?php

class Gri_Customer_Block_Address_Edit extends Mage_Customer_Block_Address_Edit
{
    protected function _prepareLayout()
    {
        /* @var $breadcrumbs Mage_Page_Block_Html_Breadcrumbs */
        $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
        $breadcrumbs and $breadcrumbs->addCrumb('home', array('label' => $this->__('Home'), 'title' => $this->__('Home'), 'link' => Mage::getBaseUrl()));
        $breadcrumbs->addCrumb('my-account', array('label' => $this->__('My Account'), 'title' => $this->__('My Account')));
        $breadcrumbs->addCrumb('address-book', array('label' => $this->__('Address Book'), 'title' => $this->__('Address Book'), 'link' => $this->getUrl('customer/address/')));
        if($this->getRequest()->getParam('id')) {
           $breadcrumbs->addCrumb('edit-address', array('label' => $this->__('Edit Address'), 'title' => $this->__('Edit Address')));
        } else {
           $breadcrumbs->addCrumb('add-new-address', array('label' => $this->__('Add New Address'), 'title' => $this->__('Add New Address')));
        }
        return parent::_prepareLayout();
    }
}