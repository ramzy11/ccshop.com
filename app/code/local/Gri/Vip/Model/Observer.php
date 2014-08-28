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
       //if(Mage::helper('gri_vip')->getEnableOfflineVIP()){
         //array('account_controller' => $this, 'customer' => $customer);
		$event = $observer->getEvent();
        $request = $event->getAccountController()->getRequest();
        $customer = $event->getCustomer();
		$this->vipHandle($customer);		

		/*
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
         }*/
       //}
     //return $this;
    }

    /**
     * Get offline vip group id
     */
    public function getOfflineVipGroupId()
    {
        return Mage::helper('gri_vip')->getGroupIdByVipLevel('offlinevip');
    }

	/**
	 * When a customer logs in, we need to get information from AS400 for the VIP data 
	 */
	public function getVipInfo(Varien_Event_Observer $observer)
	{
		$event = $observer->getEvent();
		$customer = $event->getCustomer();
		
		$this->vipHandle($customer);
	}

	private function vipHandle(Mage_Customer_Model_Customer $user)
	{
		$vipAs400 = Mage::getSingleton('gri_vip/vip_as400');

		//send request to AS400 fetching vip information
		$groups = $vipAs400->getCustomerGroup();

		$vipPk = Mage::getModel('gri_vip/offline_pk')->load($user->getId(),'customer_id');
		$last_update = "";		
		if($vipPk->getId())
		{
			
			$last_update = date('Ymd',strtotime($vipPk->getLastUpdate()));
			$vipContent = $vipAs400->checkVipInfo($vipPk->getOfflineVipId());
			$offVip = $vipContent['vipinfo'][0];
		}
		else
		{
			$vipPk = Mage::getModel('gri_vip/offline_pk');
			/*$mobile = $user->getMobilePhone();
			$country = $user->getCountry();
			$firstName = $user->getFirstname();
			$lastName = $user->getLastName();
			$email = $user->getEmail();

			
			$vipContent = $vipAs400->checkVipAccount(array('mobile'=>$mobile,'country'=>$country,'name'=>$name,'email'=>$email));
			$offVip = $vipContent['viplist'][0];
			*/
			$vipPk->setCustomerId($user->getId());
		}			
		

		$mobile = preg_replace("/[^0-9]/","",$offVip['mobile']);

		$offline_last_update = date('Ymd',strtotime($offVip['last_update']));
		
		/* We only update
		if($offline_last_update > $last_update)
		{*/
			
			$vipGroup = $offVip['grade'];
			$vipPoint = $offVip['current vip point'];
			$vipCardNo = $offVip['cardno'];
			$vipOfflinePk = $offVip['pk#'];
			$expiryDate = $offVip['expiry_date'];
			$vipExpiryDate =  substr($expiryDate,0,4).'-'.substr($expiryDate,4,2).'-'.substr($expiryDate,6,2);
			$vipLastUpdate = substr($offVip['amend_date'],0,4).'-'.substr($offVip['amend_date'],4,2).'-'.substr($offVip['amend_date'],6,2);
			
			Mage::Log($vipGroup." ".$vipPoint." ".$vipCardNo." ".$vipOfflinePk." ".$vipExpiryDate." ".$vipLastUpdate,7,'gri-debug.log');

			$localVipGroup = $vipAs400->offlineGradeMapping($vipGroup);

			$vipPk->setVipGrade($vipGroup);
			$vipPk->setVipPoint($vipPoint);
			$vipPk->setVipCardNo($vipCardNo);
			$vipPk->setOfflineVipId($vipOfflinePk);
			$vipPk->setExpiryDate($vipExpiryDate);
			$vipPk->setLastUpdate($vipLastUpdate);
			
			/*Save record for other use */
			try
			{
				$vipPk->save();
			}
			catch(Exception $e)
			{
				Mage::Log('Error getting VIP information for customer '.$user->getId(),7,'gri-debug.log');
			}
		
			/*update customer group Id */
			try
			{
				//$user->setGroupId($groups[strtolower($vipGroup)]);
				$user->setGroupId($groups[strtolower($localVipGroup)]);
				$user->save();
			}
			catch(Exception $e)
			{
				Mage::Log('Error saving customer group Id for customer '.$user->getId(),7,'gri-debug.log');
			}
		//}
	}
}
