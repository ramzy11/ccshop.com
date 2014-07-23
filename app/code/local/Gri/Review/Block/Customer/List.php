<?php

class Gri_Review_Block_Customer_List extends Mage_Review_Block_Customer_List
{
    protected function _prepareLayout()
    {
        $this->getLayout()->getBlock('head')->setTitle($this->__('Product Reviews'));
        /* @var $breadcrumbs Mage_Page_Block_Html_Breadcrumbs */
        $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
        $breadcrumbs and $breadcrumbs->addCrumb('home', array('label' => $this->__('Home'), 'title' => $this->__('Home'), 'link' => Mage::getBaseUrl()));
        $breadcrumbs->addCrumb('my-account', array('label' => $this->__('My Account'), 'title' => $this->__('My Account')));
        $breadcrumbs->addCrumb('product-reviews', array('label' => $this->__('Product Reviews'), 'title' => $this->__('Product Reviews')));

        $toolbar = $this->getLayout()->createBlock('page/html_pager', 'customer_review_list.toolbar')->setCollection($this->getCollection());
        $this->setChild('toolbar', $toolbar);
        return parent::_prepareLayout();
    }
}