<?php

/**
 * Weibo config model
 *
 * @category    Inchoo
 * @package     Inchoo_Weibo
 * @author      Ivan Weiler <ivan.weiler@gmail.com>
 * @copyright   Inchoo (http://inchoo.net)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Inchoo_Weibo_Model_Config {

    const XML_PATH_ENABLED = 'customer/weibo/enabled';
    const XML_PATH_ENABLEDSHAREFOLLOWUS = 'customer/weibo/enabled_share_follow_us';
    const XML_PATH_API_KEY = 'customer/weibo/api_key';
    const XML_PATH_SECRET = 'customer/weibo/secret';
    const XML_PATH_LOCALE = 'customer/weibo/locale';
    const XML_PATH_REDIRECT_URI = 'customer/weibo/redirect_uri';
    const XML_PATH_WEIBO_FOLLOWUS = 'customer/weibo/follow_us';
    const XML_PATH_WEIBO_SHARE = 'customer/weibo/share';

    public function getFollowUsUrl($storeId=null){
        return Mage::getStoreConfig(self::XML_PATH_WEIBO_FOLLOWUS,$storeId);
    }

    public function getShareUrl($storeId=null){
        return Mage::getStoreConfig(self::XML_PATH_WEIBO_SHARE,$storeId);
    }

    public function isEnabled($storeId = null) {
        if (Mage::getStoreConfigFlag(self::XML_PATH_ENABLED, $storeId) &&
                $this->getApiKey($storeId) &&
                $this->getSecret($storeId)) {
            return true;
        }

        return false;
    }

    public function isEnabledShareAndFollowUs($storeId=null)
    {
        return Mage::getStoreConfig(self::XML_PATH_ENABLEDSHAREFOLLOWUS, $storeId);
    }

    public function getApiKey($storeId = null) {
        return trim(Mage::getStoreConfig(self::XML_PATH_API_KEY, $storeId));
    }

    public function getSecret($storeId = null) {
        return trim(Mage::getStoreConfig(self::XML_PATH_SECRET, $storeId));
    }

    public function getRequiredPermissions() {
        return array('email');
    }

    public function getLocale($storeId = null) {
        return Mage::getStoreConfig(self::XML_PATH_LOCALE, $storeId);
    }

    public function getRedirectUri($storeId = null) {
        return trim(Mage::getStoreConfig(self::XML_PATH_REDIRECT_URI, $storeId));
    }

}