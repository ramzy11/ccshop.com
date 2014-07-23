<?php

class Gri_Affiliate_Model_Affiliate_Chinesean extends Gri_Affiliate_Model_Affiliate_Abstract
{
    const CONFIG_PATH_CONVERT_CURRENCY = 'gri_affiliate/chinesean/convert_currency';
    const CONFIG_PATH_COOKIE_LIFE = 'gri_affiliate/chinesean/cookie_life';
    const CONFIG_PATH_CURRENCY = 'gri_affiliate/chinesean/currency';
    const CONFIG_PATH_ENABLED = 'gri_affiliate/chinesean/enabled';
    const CONFIG_PATH_HASH_KEY = 'gri_affiliate/chinesean/hash_key';
    const CONFIG_PATH_PID = 'gri_affiliate/chinesean/pid';
    const CONFIG_PATH_SENT_LIMIT = 'gri_affiliate/chinesean/sent_limit';

    protected function _construct()
    {
        $this->setCode('chinesean');
    }

    protected function _getTrackingHtml()
    {
        $order = $this->getAffiliateOrder()->getOrder();
        $pid = Mage::getStoreConfig(self::CONFIG_PATH_PID);
        $hashKey = Mage::getStoreConfig(self::CONFIG_PATH_HASH_KEY);
        $scheme = $this->getRequest()->getScheme();
        $orderId = $order->getIncrementId();
        $amount = $order->getGrandTotal();
        if (Mage::getStoreConfig(self::CONFIG_PATH_CONVERT_CURRENCY) &&
            ($toCurrency = Mage::getStoreConfig(self::CONFIG_PATH_CURRENCY)) != ($currency = $order->getBaseCurrencyCode())
        ) try {
            $amount = sprintf('%.2f', $this->getCurrencyConverter()->load($currency)->convert($amount, $toCurrency));
        }
        catch (Exception $e) {
            Mage::logException($e);
        }
        $url = '%s://www.chinesean.com/affiliate/trackingPixel1.do?pId=%s&tracking=%s&cpa=&cpl=&cps=%s,TIERID';
        $url = sprintf($url, $scheme, $pid, $orderId, $amount);
        $hashKey and $url .= '&hash=' . sha1($orderId . 'TIERID' . $hashKey);
        $html = '<!-- pixel start -->
<iframe src="%s" scrolling="no" frameborder="0" width="1" height="1"></iframe>
<!-- pixel end -->
';
        return sprintf($html, $url);
    }

    protected function _identify()
    {
        return $this;
    }

    public function getCookieLifeTime()
    {
        return 86400 * Mage::getStoreConfig(self::CONFIG_PATH_COOKIE_LIFE);
    }

    public function isEnabled()
    {
        return Mage::getStoreConfig(self::CONFIG_PATH_ENABLED);
    }

    public function getSentLimit()
    {
        return Mage::getStoreConfig(self::CONFIG_PATH_SENT_LIMIT);
    }
}
