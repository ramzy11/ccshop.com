<?php
class Magiatec_Common_Model_Feed extends Mage_AdminNotification_Model_Feed
{
    const XML_FREQUENCY_PATH = 'magiatec/feed/check_frequency';
    const XML_FEED_URL       = 'http://colorswatchextension.com/feed.xml';
    
    public static function check()
    {
        return Mage::getModel('magiatec/feed')->checkUpdate();
    }
    
    public function getFrequency()
    {
        return Mage::getStoreConfig(self::XML_FREQUENCY_PATH) * 3600;
    }
    
    public function getLastUpdate()
    {
        return Mage::app()->loadCache('magiatec_notifications_lastcheck');
    }
    
    public function setLastUpdate()
    {
        Mage::app()->saveCache(time(), 'magiatec_notifications_lastcheck');
        return $this;
    }
    
    public function getFeedUrl()
    {
        if (is_null($this->_feedUrl)) {
            $this->_feedUrl = self::XML_FEED_URL;
        }
        $query = '?base_url=' . urlencode(Mage::getStoreConfig('web/unsecure/base_url'));
        return $this->_feedUrl . $query;
    }
    
    public function checkUpdate()
    {
        if (($this->getFrequency() + $this->getLastUpdate()) > time()) {
            return $this;
        }
        
        $feedData = array();
        
        $feedXml = $this->getFeedData();
        
        if ($feedXml && $feedXml->channel && $feedXml->channel->item) {
            foreach ($feedXml->channel->item as $item) {
                $feedData[] = array(
                    'severity' => (int)$item->severity ? (int)$item->severity : 3,
                    'date_added' => $this->getDate((string)$item->pubDate),
                    'title' => (string)$item->title,
                    'description' => (string)$item->description,
                    'url' => (string)$item->link,
                );
            }
            if ($feedData) {
                Mage::getModel('adminnotification/inbox')
                        ->parse(array_reverse($feedData));
            }
        }
    }
}
