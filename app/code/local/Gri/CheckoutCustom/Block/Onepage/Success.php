<?php

class Gri_CheckoutCustom_Block_Onepage_Success extends Mage_Checkout_Block_Onepage_Success
{

    public function _prepareLayout()
    {
        /* @var $breadcrumbs Mage_Page_Block_Html_Breadcrumbs */
        $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
        $breadcrumbs and $breadcrumbs->addCrumb('home', array('label' => $this->__('Home'), 'title' =>$this->__('Home'),'link'=> Mage::getBaseUrl()));

        $breadcrumbs->addCrumb('checkout', array('label' => $this->__('Checkout'), 'title' => $this->__('Checkout')));
        return parent::_prepareLayout();
    }

    protected function _construct()
    {
        parent::_construct();
        Mage::register('current_order', $this->getOrder());
    }

    public function getOrder()
    {
        if ($this->getData('order') === NULL) {
            /* @var $order Mage_Checkout_Block_Onepage_Success */
            $order = Mage::getModel('sales/order')->load(Mage::getSingleton('checkout/session')->getLastOrderId());
            $this->setData('order', $order);
        }
        return $this->getData('order');
    }
}
