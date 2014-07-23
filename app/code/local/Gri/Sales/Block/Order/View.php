<?php

class Gri_Sales_Block_Order_View extends Mage_Sales_Block_Order_View
{
    protected function _prepareLayout()
    {
        /* @var $breadcrumbs Mage_Page_Block_Html_Breadcrumbs */
        $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
        $breadcrumbs and $breadcrumbs->addCrumb('home', array('label' => $this->__('Home'), 'title' => $this->__('Home'), 'link' => Mage::getBaseUrl()));
        $breadcrumbs->addCrumb('myaccount', array('label' => $this->__('My Account'), 'title' =>$this->__('My Account')));
        $breadcrumbs->addCrumb('orders', array('label' => $this->__('Orders'), 'title' => $this->__('Orders'), 'link' => $this->getUrl('sales/order/history/')));
        $breadcrumbs->addCrumb('order-detail', array('label' => $this->__('Order Detail'), 'title' =>$this->__('Order Detail')));
        return parent::_prepareLayout();
    }
}