<?php

/**
 * Data helper
 *
 * @category    Inchoo
 * @package     Inchoo_Facebook
 * @author      Ivan Weiler <ivan.weiler@gmail.com>
 * @copyright   Inchoo (http://inchoo.net)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Inchoo_Facebook_Helper_Data extends Mage_Core_Helper_Abstract {

    public function getConnectUrl() {
        return $this->_getUrl('inchoo_facebook/customer_account/connect', array('_secure' => true));
    }

    public function isFacebookCustomer($customer) {
        if ($customer->getFacebookUid()) {
            return true;
        }
        return false;
    }
    
    public function isEnabled(){
        return Mage::getSingleton('inchoo_facebook/config')->isEnabled();  
    }

    public  function isEnabledShareAndFollowUs(){
        return  Mage::getSingleton('inchoo_facebook/config')->isEnabledShareAndFollowUs();
    }

    public function getFollowUsUrl(){
        return Mage::getSingleton('inchoo_facebook/config')->getFollowUsUrl();
    }

    public function getShareUrl(){
        return Mage::getSingleton('inchoo_facebook/config')->getShareUrl();
    }
    public function getApiKey(){
        return Mage::getSingleton('inchoo_facebook/config')->getApiKey();
    }

    public function getSecret(){
        return Mage::getSingleton('inchoo_facebook/config')->getSecret();
    }

}