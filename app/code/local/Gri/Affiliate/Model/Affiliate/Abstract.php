<?php

/**
 * Class Gri_Affiliate_Model_Affiliate_Abstract
 *
 * @method Gri_Affiliate_Model_Order getAffiliateOrder() Get affiliate order
 * @method string getCode() Get affiliate code
 * @method Gri_Affiliate_Model_Affiliate_Abstract setAffiliateOrder(Gri_Affiliate_Model_Order $affiliateOrder) Set affiliate order
 * @method Gri_Affiliate_Model_Affiliate_Abstract setCode(string $code) Set affiliate code
 */
abstract class Gri_Affiliate_Model_Affiliate_Abstract extends Varien_Object
{
    const COOKIE_PATH_AFFILIATE = 'gri_affiliate';
    const COOKIE_PATH_LANDING_PAGE = 'gri_affiliate_landing_page';
    const COOKIE_PATH_LANDING_TIME = 'gri_affiliate_landing_time';

    abstract protected function _getTrackingHtml();
    abstract protected function _identify();
    abstract public function getCookieLifeTime();
    abstract public function getSentLimit();
    abstract public function isEnabled();

    protected function _getHash()
    {
        return $this->getCode() . Mage::app()->getCookie()->get(self::COOKIE_PATH_LANDING_TIME);
    }

    /**
     * @return Mage_Directory_Model_Currency
     */
    public function getCurrencyConverter()
    {
        return Mage::getSingleton('directory/currency');
    }

    public function getHash()
    {
        return md5($this->_getHash());
    }

    public function getLandingPage()
    {
        return Mage::app()->getCookie()->get(self::COOKIE_PATH_LANDING_PAGE);
    }

    public function getRequest()
    {
        return Mage::app()->getRequest();
    }

    public function getTrackingHtml()
    {
        if ($this->isEnabled() &&
            $this->isInDate() &&
            $this->getAffiliateOrder()->getOrder()
        ) {
            return $this->_getTrackingHtml();
        }
        return '';
    }

    public function isInDate()
    {
        return Mage::app()->getCookie()->get(self::COOKIE_PATH_LANDING_TIME) + $this->getCookieLifeTime() > time();
    }

    public function identify()
    {
        $this->_identify();
        Mage::app()->getCookie()->set(self::COOKIE_PATH_AFFILIATE, $this->getCode(), $this->getCookieLifeTime())
            ->set(self::COOKIE_PATH_LANDING_PAGE, Mage::helper('core/url')->getCurrentUrl(), $this->getCookieLifeTime())
            ->set(self::COOKIE_PATH_LANDING_TIME, time(), $this->getCookieLifeTime())
        ;
    }
}
