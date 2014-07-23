<?php
class Gri_CheckoutCustom_Block_Cart extends Mage_Checkout_Block_Cart
{
    public function _prepareLayout()
    {
        /* @var $breadcrumbs Mage_Page_Block_Html_Breadcrumbs */
        $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
        $breadcrumbs and $breadcrumbs->addCrumb('home', array('label' => $this->__('Home'), 'title' =>$this->__('Home'),'link'=> Mage::getBaseUrl()));

        $breadcrumbs->addCrumb('review-your-tote', array('label' => $this->__('Review Your Tote'), 'title' =>$this->__('Review Your Tote')));
        return parent::_prepareLayout();
    }

}