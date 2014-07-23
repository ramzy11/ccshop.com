<?php

/**
 * Customer front  newsletter manage block
 *
 * @category   Gri
 * @package    Gri_Customer
 * @author     Gri Team <kanelu@bcnetcom.com>
 */
class Gri_Customer_Block_Newsletter extends Mage_Customer_Block_Newsletter
{
    public function _prepareLayout()
    {
        $this->getLayout()->getBlock('head')->setTitle($this->__('Newslettter'));
        /* @var $breadcrumbs Mage_Page_Block_Html_Breadcrumbs */
        $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
        $breadcrumbs and $breadcrumbs->addCrumb('home', array('label' => $this->__('Home'), 'title' =>$this->__('Home'),'link'=> Mage::getBaseUrl()));

        $breadcrumbs->addCrumb('myaccount', array('label' => $this->__('My Account'), 'title' =>$this->__('My Account'),'link'=> $this->getUrl('customer/account')));
        $breadcrumbs->addCrumb('newslettter', array('label' => $this->__('Newslettter'), 'title' =>$this->__('Newslettter')));

        return parent::_prepareLayout();
    }
}
