<?php

class Gri_GiftCardAccount_ProductController extends Mage_Core_Controller_Front_Action
{

    /**
     * @return Mage_Customer_Model_Session
     */
    protected function _getCustomerSession()
    {
        return Mage::getSingleton('customer/session');
    }

    /**
     * @return Gri_GiftCardAccount_Helper_Product
     */
    protected function _getHelper()
    {
        return Mage::helper('gri_giftcardaccount/product');
    }

    /**
     * Load reward by customer
     *
     * @return Gri_Reward_Model_Reward
     */
    protected function _getReward()
    {
        $reward = Mage::getModel('gri_reward/reward')
            ->setCustomer($this->_getCustomerSession()->getCustomer())
            ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
            ->loadByCustomer();
        return $reward;
    }

    /**
     * @return Gri_CatalogCustom_Model_Product|boolean
     */
    protected function _prepareGiftCardProduct()
    {
        /* @var $product Gri_CatalogCustom_Model_Product */
        $product = Mage::getModel('catalog/product')->load($this->getRequest()->getParam('id'));
        $product->getId() && $product->isGiftCard() or $product = FALSE;
        return $product;
    }

    public function redeemAction()
    {
        $data = array(
            'error' => 0,
            'message' => '',
        );
        if ($this->_getCustomerSession()->isLoggedIn()) {
            if ($product = $this->_prepareGiftCardProduct()) {
                $reward = $this->_getReward();
                if ($reward->getCustomerPointsBalance() >= $product->getRewardPoints()) {
                    $result = $this->_getHelper()->redeemGiftCardWithRewardPoints($product, $reward);
                    if ($result instanceof Gri_GiftCardAccount_Model_Giftcardaccount) {
                        $data['message'] = $this->__('Redeem success! Your gift card code is %s and has been sent to your email', $result->getCode());
                    }
                    else {
                        $data['error'] = 1;
                        $data['message'] = $result;
                    }
                }
                else {
                    $data['error'] = 1;
                    $data['message'] = $this->__('Insufficient reward points.');
                }
            }
            else {
                $data['error'] = 1;
                $data['message'] = $this->__('Invalid gift card product.');
            }
        }
        else {
            $data['error'] = 2;
            $data['message'] = $this->__('Please login first.');
        }
        $data = Zend_Json::encode($data);
        // JSONP callback - for cross-domain ajax invocation
        ($callback = $this->getRequest()->getParam('callback')) and $data = $callback . '(' . $data . ');';
        $this->getResponse()->setHeader('Content-type', $callback ? 'text/javascript' : 'text/json')->setBody($data);
    }
}
