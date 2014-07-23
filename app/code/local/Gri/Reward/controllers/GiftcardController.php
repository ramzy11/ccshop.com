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
 * Reward customer controller
 *
 * @category    Gri
 * @package     Gri_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Gri_Reward_GiftcardController extends Mage_Core_Controller_Front_Action {

    /**
     * Retrieve customer session model object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession() {
        return Mage::getSingleton('customer/session');
    }

    /**
     * Retrieve customer session model object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getCustomer() {
        return $this->_getSession()->getCustomer();
    }

    /**
     * Load reward by customer
     *
     * @return Gri_Reward_Model_Reward
     */
    protected function _getReward() {
        $reward = Mage::getModel('gri_reward/reward')
                ->setCustomer($this->_getCustomer())
                ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                ->loadByCustomer();
        return $reward;
    }

    /**
     *  redeem gift product
     * 
     */
    public function redeemPostAction() {
        $success = true ;
        $message = '';
        
        $product_id = $this->getRequest()->getParam('product_id', false);
        $giftcard_amount = $this->getRequest()->getParam('amount',0);
        $product = Mage::getSingleton('catalog/product')->load($product_id);
        $reward = Mage::getSingleton('gri_reward/reward');   
        
        $errCode = 1;
        if( !$this->_getCustomer()->getId() ){
          $success = false;
          $message = 'Need to login';
          $errCode = -1;
        }
        
        if( true == $success && !$product_id ){
          $success = false;
          $message = 'Unvalid Product Id';
          $errCode = -2;
        }
        
        if( true == $success && !$giftcard_amount ){
          $success = false;
          $message = 'Not Enough Reward Point';
          $errCode = -2;
        }
                                
        if( $success == true && !$reward->redeemPointsByGiftCard($product,$giftcard_amount) ){
          $success = false;
          $message = 'Redeem Failured';
          $errCode = -3;
        }
        
        $message = $this->__($message);
        
        //output 
        exit(Zend_Json::encode(array('success'=>$success,'message'=>$message,'errCode'=>$errCode)));
    }
    
    /**
     *  @param  $amount
     *  
     *  @return int
     */
    public function calculateToPoints($amount){
        $rate = Mage::getSingleton('gri_reward/reward_rate');
        return  $rate->calculateToPoints($amount);
    }
         
    /**
     * @return  Mage_Core_Model_Session
     */
    public  function  getSession(){
        return  Mage::getSingleton('core/session');
    }
    
}