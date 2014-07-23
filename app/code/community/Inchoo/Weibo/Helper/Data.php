<?php

/**
 * Data helper
 *
 * @category    Inchoo
 * @package     Inchoo_Weibo
 * @author      Ivan Weiler <ivan.weiler@gmail.com>
 * @copyright   Inchoo (http://inchoo.net)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Inchoo_Weibo_Helper_Data extends Mage_Core_Helper_Abstract {

    public function getConnectUrl() {
        return $this->_getUrl('inchoo_weibo/customer_account/connect', array('_secure' => true));
    }

    public function isWeiboCustomer($customer) {
        if ($customer->getWeiboUid()) {
            return true;
        }
        return false;
    }
    
    /**
     *  @return   url of  weibo rebind
     */
    public function getRebindPostUrl(){
       return $this->_getUrl('inchoo_weibo/customer_account/rebindpost');   
    }
    
    public  function getSuccessUrl(){
      return $this->_getUrl('customer/account');   
    }
    
    /**
     *  get weibo uid
     *  
     */
    public function getWeiboUid() {
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        return $customer->load(null)->getWeiboUid();
    }
    
    public function isEnabled(){
        return Mage::getSingleton('inchoo_weibo/config')->isEnabled();  
    }

    public function getFollowUsUrl(){
        return Mage::getSingleton('inchoo_weibo/config')->getFollowUsUrl();
    }

    public function  getShareUrl(){
         return  Mage::getSingleton('inchoo_weibo/config')->getShareUrl();
    }

    public function isEnabledShareAndFollowUs(){
        return  Mage::getSingleton('inchoo_weibo/config')->isEnabledShareAndFollowUs();
    }
}