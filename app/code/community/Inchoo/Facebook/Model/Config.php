<?php
/**
 * Facebook config model
 *
 * @category    Inchoo
 * @package     Inchoo_Facebook
 * @author      Ivan Weiler <ivan.weiler@gmail.com>
 * @copyright   Inchoo (http://inchoo.net)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Inchoo_Facebook_Model_Config
{
	const XML_PATH_ENABLED = 'customer/facebook/enabled';
    const XML_PATH_ENABLEDSHAREFOLLOWUS = 'customer/facebook/enabled_share_follow_us';
	const XML_PATH_API_KEY = 'customer/facebook/api_key';
	const XML_PATH_SECRET = 'customer/facebook/secret';
	const XML_PATH_LOCALE = 'customer/facebook/locale';
    const XML_PATH_FACEBOOK_FOLLOWUS = 'customer/facebook/follow_us';

    const XML_PATH_FACEBOOK_SHARE = 'customer/facebook/share';

    public function getFollowUsUrl($storeId=null){
        return Mage::getStoreConfig(self::XML_PATH_FACEBOOK_FOLLOWUS,$storeId);
    }

    public function getShareUrl($storeId=null){
        return Mage::getStoreConfig(self::XML_PATH_FACEBOOK_SHARE,$storeId);
    }

    public function isEnabled($storeId=null)
    {
		if( Mage::getStoreConfigFlag(self::XML_PATH_ENABLED, $storeId) && 
			$this->getApiKey($storeId) && 
			$this->getSecret($storeId))
		{
        	return true;
        }
        
        return false;
    }

    public function isEnabledShareAndFollowUs($storeId=null)
    {
       return Mage::getStoreConfig(self::XML_PATH_ENABLEDSHAREFOLLOWUS, $storeId);
    }
	
    public function getApiKey($storeId=null)
    {
    	return trim(Mage::getStoreConfig(self::XML_PATH_API_KEY, $storeId));
    }
    
    public function getSecret($storeId=null)
    {
    	return trim(Mage::getStoreConfig(self::XML_PATH_SECRET, $storeId));
    }
    
    public function getRequiredPermissions()
    {
    	return array('email');
    }
    
    public function getLocale($storeId=null)
    {
    	return Mage::getStoreConfig(self::XML_PATH_LOCALE, $storeId);
    }
}