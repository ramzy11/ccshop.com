<?php

/**
 * Gri customer helper
 */
class Gri_Customer_Helper_Data extends Mage_Core_Helper_Abstract {
    const CONFIG_PATH_ONLINE_CHAT_ENABLED = 'customer/online_chat/enabled';
    const CONFIG_PATH_YOUTUBE_ENABLED = 'customer/youtube/enabled';
    const CONFIG_PATH_TWITTER_ENABLED = 'customer/twitter/enabled';
    const CONFIG_PATH_PINTEREST_ENABLED = 'customer/pinterest/enabled';
    const CONFIG_PATH_INSTAGRAM_ENABLED = 'customer/instagram/enabled';


    const CONFIG_PATH_YOUTUBE_FOLLOWUS = 'customer/youtube/follow_us';
    const CONFIG_PATH_TWITTER_FOLLOWUS = 'customer/twitter/follow_us';
    const CONFIG_PATH_PINTEREST_FOLLOWUS = 'customer/pinterest/follow_us';
    const CONFIG_PATH_INSTAGRAM_FOLLOWUS = 'customer/instagram/follow_us';

    const CONFIG_PATH_TWITTER_SHARE = 'customer/twitter/share';
    const CONFIG_PATH_PINTEREST_SHARE = 'customer/pinterest/share';
    const CONFIG_PATH_INSTAGRAM_SHARE = 'customer/instagram/share';

    public function enabledOnlineChat()
    {
        return $this->_getStoreConfig(self::CONFIG_PATH_ONLINE_CHAT_ENABLED);
    }

    public function enabledYoutube()
    {
        return $this->_getStoreConfig(self::CONFIG_PATH_YOUTUBE_ENABLED);
    }

    public  function enabledTwitter()
    {
        return $this->_getStoreConfig(self::CONFIG_PATH_TWITTER_ENABLED);
    }

    public function enabledPinterest()
    {
        return $this->_getStoreConfig(self::CONFIG_PATH_PINTEREST_ENABLED);
    }

    public function enabledInstagram()
    {
        return $this->_getStoreConfig(self::CONFIG_PATH_INSTAGRAM_ENABLED);
    }

    public function getYoutubeFollowUsUrl()
    {
        return $this->_getFollowUsUrl(self::CONFIG_PATH_YOUTUBE_FOLLOWUS);
    }

    public function getTwitterFollowUsUrl()
    {
        return $this->_getFollowUsUrl(self::CONFIG_PATH_TWITTER_FOLLOWUS);
    }

    public function getPinterestFollowUsUrl()
    {
        return $this->_getFollowUsUrl(self::CONFIG_PATH_PINTEREST_FOLLOWUS);
    }

    public function getInstagramFollowUsUrl()
    {
        return $this->_getFollowUsUrl(self::CONFIG_PATH_INSTAGRAM_FOLLOWUS);
    }

    public function getTwitterShareUrl()
    {
        return $this->_getStoreConfig(self::CONFIG_PATH_TWITTER_SHARE);
    }

    public function getPinterestShareUrl()
    {
        return $this->_getStoreConfig(self::CONFIG_PATH_PINTEREST_SHARE);
    }

    public function getInstagramShareUrl()
    {
        return $this->_getStoreConfig(self::CONFIG_PATH_INSTAGRAM_SHARE);
    }

    protected function _getFollowUsUrl($path)
    {
        $followUsUrl = $this->_getStoreConfig($path);
        if(empty($followUsUrl)){
            $followUsUrl = '#';
        }

        return $followUsUrl;
    }

    protected function _getStoreConfig($path)
    {
        return Mage::getStoreConfig($path);
    }
    public function getGroupNameById($groupId)
    {
        if(intval($groupId)){
            $groups = Mage::helper('customer')->getGroups()->getData();
            foreach($groups as $group){
                if($group['customer_group_id'] == $groupId ){
                    return $group['customer_group_code'];
                }
            }
        }
        else{
           return 'Guest';
        }
    }
    
}
