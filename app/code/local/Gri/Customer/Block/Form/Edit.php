<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Customer
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer edit form block
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Gri_Customer_Block_Form_Edit extends Mage_Customer_Block_Form_Edit
{
    protected function _prepareLayout()
    {
        $this->getLayout()->getBlock('head')->setTitle(Mage::helper('customer')->__('Account Info'));
        /* @var $breadcrumbs Mage_Page_Block_Html_Breadcrumbs */
        $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
        $breadcrumbs and $breadcrumbs->addCrumb('home', array('label' => $this->__('Home'), 'title' => $this->__('Home'), 'link' => Mage::getBaseUrl()));
        $breadcrumbs->addCrumb('my-account', array('label' => $this->__('My Account'), 'title' => $this->__('My Account')));
        $breadcrumbs->addCrumb('account-info', array('label' => $this->__('Account Info'), 'title' => $this->__('Account Info')));
        return parent::_prepareLayout();
    }

    public function getIsSubscribed(){
        $customerId = $this->getCustomer()->getId();
        return Mage::getSingleton('newsletter/subscriber')->load($customerId,'customer_id')->isSubscribed();
    }

}
