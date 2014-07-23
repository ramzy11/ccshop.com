<?php

/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
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
 * @package     Gri_GiftCardAccount
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */
class Gri_GiftCardAccount_CustomerController extends Mage_Core_Controller_Front_Action {

    /**
     * Only logged in users can use this functionality,
     * this function checks if user is logged in before all other actions
     *
     */
    public function preDispatch() {
        parent::preDispatch();

        if (!Mage::getSingleton('customer/session')->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
        }
    }

    /**
     * Redeem gift card
     *
     */
    public function indexAction() {
        $data = $this->getRequest()->getPost();
        if (isset($data['giftcard_code'])) {
            $code = $data['giftcard_code'];
            try {
                if (!Mage::helper('gri_customerbalance')->isEnabled()) {
                    Mage::throwException($this->__('Redemption functionality is disabled.'));
                }
                Mage::getModel('gri_giftcardaccount/giftcardaccount')->loadByCode($code)
                        ->setIsRedeemed(true)->redeem();
                Mage::getSingleton('customer/session')->addSuccess(
                        $this->__('Gift Card "%s" was redeemed.', Mage::helper('core')->htmlEscape($code))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('customer/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('customer/session')->addException($e, $this->__('Cannot redeem Gift Card.'));
            }
            $this->_redirect('*/*/*');
            return;
        }
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->loadLayoutUpdates();
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle($this->__('Gift Card'));
        }
        $this->renderLayout();
    }

    /**
     *   convert  reward points to  gift card 
     *   
     *   function of  saving  points  ?????   in line    
     */
    public function convertAction() {
        $data = $this->getRequest()->getPost();
        if (isset($data['reward_points']) && isset($data['receiver_email'])) {
            $reward_points = intval($data['reward_points']);
            $receiver_email = $data['receiver_email'];
            try {
                if (!Mage::helper('gri_customerbalance')->isEnabled()) {
                    Mage::throwException($this->__('Convertion functionality is disabled.'));
                }
                //customer id
                $customer_id = Mage::getSingleton('customer/session')->getCustomer()->getEntityId();
                
                // Return first free code
                $rand_gift_card_code = '';
                $gca_pool = Mage::getSingleton('gri_giftcardaccount/pool');
                $rand_gift_card_code = $gca_pool->shift();
                
                //get  points balance
                $pointBalance = Mage::getSingleton('gri_reward/reward')->load($customer_id,'customer_id')->getPointsBalance();
                //!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!//
                if($pointBalance < $reward_points){
                   Mage::getSingleton('customer/session')->addError('reward points more than num in your account');
                }
                              
                // 把reward points 转换成 amount
                $rate = Mage::getSingleton('gri_reward/reward_rate')->load(2);
                $currencyAmount = $rate->getCurrencyAmount() ;
                $points = $rate->getPoints();
                $convertedAmount = $reward_points * ($currencyAmount / $points);
                
                //
                $data = array('code' => $rand_gift_card_code,
                    'status' => '1',
                    'date_created' => date('Y-m-d'),
                    // 'date_expires' => '',
                    'website_id' => 1,
                    'balance' => $convertedAmount,
                    'state' => '0',
                    'is_redeemable' => '1'
                );
                $rand_gift_card_code && $gca = Mage::getModel('gri_giftcardaccount/giftcardaccount')->setData($data);
                if ($gca->save()->getCode($rand_gift_card_code)) {
                    $gca_pool_code = $gca_pool->load($rand_gift_card_code, 'code');
                    $gca_pool_code->setStatus(0);
                    $gca_pool_code->save();
                }
                              
                // update customer  points
                //-------------------------------------------------------------------------------------//
                $reward = Mage::getModel('gri_reward/reward')->load($customer_id, 'customer_id');
                $points_balance = $reward->getPointsBalance();
                echo  $points_balance.'<br />' ;
                $points_balance = $points_balance-$reward_points;
                echo $points_balance.'<br />';
                $reward->setPointsBalance($points_balance);
                $reward->save();
                echo  $reward->getPointsBalance().'<br />';   
                //-------------------------------------------------------------------------------------//
                                 
                // notification
                $reward->sendBalanceUpdateNotification();
                
                // send code to receiver's email
                $store = Mage::app()->getStore();
                $mail = Mage::getModel('core/email_template');
                /* @var $mail Mage_Core_Model_Email_Template */
                $mail->setDesignConfig(array('area' => 'frontend', 'store' => $store->getId()));
                $templateVars = array(
                  'update_message' => $rand_gift_card_code,
                );
                $mail->sendTransactional(
                        $store->getConfig('gri_reward/notification/balance_update_template'), 
                                          $store->getConfig('gri_reward/notification/email_sender'), 
                                          $receiver_email, 
                                          null, 
                                          $templateVars, 
                                          $store->getId()
                                        );
                if ($mail->getSentSuccess()) {
                 $this->setBalanceUpdateSent(true);
                }
                Mage::getSingleton('customer/session')->addSuccess(
                   $this->__('Reward Points "%s" were converted to "%f in gift card" .',$reward_points,$convertedAmount)
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('customer/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('customer/session')->addException($e, $this->__('Cannot Convert reward Points.'));
            }
            $this->_redirect('*/*/*');
            return;
        }

        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->loadLayoutUpdates();
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle($this->__('Gift Card'));
        }
        $this->renderLayout();
    }
}