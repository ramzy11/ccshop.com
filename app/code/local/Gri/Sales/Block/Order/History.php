<?php
/**
 * Sales order history block
 *
 * @category   Gri
 * @package    Gri_Sales
 * @author     BCNETCOM Team <kanelu@bcnetcom.com>
 */

class Gri_Sales_Block_Order_History extends Mage_Sales_Block_Order_History
{
    protected function _prepareLayout()
    {
        $this->getLayout()->getBlock('head')->setTitle(Mage::helper('customer')->__('Orders'));
        /* @var $breadcrumbs Mage_Page_Block_Html_Breadcrumbs */
        $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
        $breadcrumbs and $breadcrumbs->addCrumb('home', array('label' => $this->__('Home'), 'title' =>'Home','link'=> Mage::getBaseUrl()));
        $breadcrumbs->addCrumb('myaccount', array('label' => $this->__('My Account'), 'title' =>$this->__('My Account')));
        $breadcrumbs->addCrumb('orders', array('label' => $this->__('Orders'), 'title' => $this->__('Orders')));

        parent::_prepareLayout();
        return $this;
    }
}
