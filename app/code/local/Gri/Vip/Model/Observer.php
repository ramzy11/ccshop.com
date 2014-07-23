<?php
/**
 * Magento Gri Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Gri Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/gri-edition
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
 * @category    Gri
 * @package     Gri_Reward
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/gri-edition
 */


/**
 * Reward observer
 *
 * @category    Gri
 * @package     Gri_Vip
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Gri_Vip_Model_Observer
{

    public function upgradeVip(Varien_Event_Observer $observer)
    {
        $customer = $observer->getEvent()->getCustomer();

        /* @var $vip Gri_Vip_Model_Vip */
        $vip = Mage::getSingleton('gri_vip/vip');
        $customer->getId() and $vip->upgradeCustomerGroup($customer);

        return  $this ;
    }

    public function setCustomerInfoTemplate(Varien_Event_Observer $observer)
    {
        $block = $observer->getEvent()->getBlock();
        /* @var $block Mage_Adminhtml_Block_Customer_Edit_Tab_Account */
        if (!$block instanceof Mage_Adminhtml_Block_Customer_Edit_Tab_Account) return;
        /* @var $form Varien_Data_Form */
        $form = $block->getForm();
        $customer = Mage::registry('current_customer');
        $vip = $this->_getOfflineVipInfo($customer->getEntityId());

        //$customer->setData('vip_id',$vip['vip_id']);
        //$customer->setData('vip_level',$vip['vip_level']);

        $form->setValues($customer->getData());
        foreach($form->getElements() as $fieldset) {
            if ($fieldset->getId() == 'base_fieldset') {
                $fieldset->addField('vip_id', 'text', array(
                    'label' => Mage::helper('customer')->__(' Offline VIP ID'),
                    'name'  => 'vip_id',
                    'disabled' => true,
                    'value' => $vip['vip_id']
                ));
                $fieldset->addField('vip_level', 'text', array(
                    'label' => Mage::helper('customer')->__('Offine VIP Level'),
                    'name' => 'vip_level',
                    'disabled' => true,
                    'value' => $vip['vip_level']
                ));
             break;
          }
        }
        return  $this;
    }

    /**
     * Get offline VIP info
     * @param $customerId
     * @return array
     */
    protected  function _getOfflineVipInfo($customerId)
    {
        $offline_vip = Mage::getSingleton('gri_vip/relation_offline')->load($customerId, 'customer_id');

        $card_no = $offline_vip->getCardNo();
        $card_level = $offline_vip->getCardLevel();

        $card_no = $card_no ? $card_no : '';
        $card_level = $card_level ? $card_level : '';

        return array('vip_id' => $card_no, 'vip_level' => $card_level);
    }

    public function bindOffline(Varien_Event_Observer $observer)
    {
       if(Mage::helper('gri_vip')->getEnableOfflineVIP()){
         //array('account_controller' => $this, 'customer' => $customer);
         $event = $observer->getEvent();
         $request = $event->getAccountController()->getRequest();
         $customer = $event->getCustomer();

         //request
         $mobile = $request->getPost('mobilephone');
         $offlineVip = Mage::getModel('gri_vip/relation_offline')->load($mobile, 'mobilephone');
         $checkVip = $request->getPost('checkbox2');
        
         if ($checkVip && $offlineVip->getId() && !$offlineVip->getCustomerId()) {
            $offlineVip->setCustomerId($customer->getId());
            $updated_time = Mage::getSingleton('core/locale')->date()->toString('y-MM-dd H:m:s');
            $offlineVip->setUpdateTime($updated_time);
            $offlineVip->save();

            //update offline vip group id
            if ($this->getOfflineVipGroupId()) {
                $customer->setGroupId($this->getOfflineVipGroupId());
                $customer->save();
            }
         }
       }
     return $this;
    }

    /**
     * Get offline vip group id
     */
    public function getOfflineVipGroupId()
    {
        return Mage::helper('gri_vip')->getGroupIdByVipLevel('offlinevip');
    }
}
