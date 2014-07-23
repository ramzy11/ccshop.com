<?php

class Gri_Core_Helper_Ip extends Mage_Core_Helper_Abstract
{
    const CONFIG_PATH_COUNTRY_CURRENCY = 'ip/general/content';
    const CONFIG_PATH_IP_AUTO_REDIRECT = 'ip/general/auto_redirect';
    const CONFIG_PATH_IP_ENABLED = 'ip/general/enabled';
    const CONFIG_PATH_IP_MESSAGE = 'ip/general/message';
    const CONFIG_PATH_SHOW_POPUP = 'ip/general/show_popup';
    const CONFIG_PATH_SWITCH_CURRENCY_ENABLED = 'ip/general/enable_switch_currency';

    protected $_currencyGroups;
    protected $_showPopUp;
    protected $_sites;

    public function autoRedirect()
    {
        return Mage::getStoreConfig(self::CONFIG_PATH_IP_AUTO_REDIRECT);
    }

    public function enabled()
    {
        return Mage::getStoreConfig(self::CONFIG_PATH_IP_ENABLED);
    }

    public function enableSwitchCurrency()
    {
        return Mage::getStoreConfig(self::CONFIG_PATH_SWITCH_CURRENCY_ENABLED);
    }

    /**
     * @return array
     */
    public function getCurrencyGroups()
    {
        if ($this->_currencyGroups === NULL) {
            $result = array();
            if ($this->enableSwitchCurrency()) {
                $groups = trim(Mage::getStoreConfig(self::CONFIG_PATH_COUNTRY_CURRENCY));
                $groups = explode("\n", str_replace("\n\n", "\n", str_replace("\r", "\n", $groups)));
                foreach ($groups as $group) {
                    $group = explode(':', trim($group));
                    isset($group[1]) and $result[$group[0]] = $group[1];
                }
            }
            $this->_currencyGroups = $result;
        }
        return $this->_currencyGroups;
    }

    public function getMessage()
    {
        return Mage::getStoreConfig(self::CONFIG_PATH_IP_MESSAGE);
    }

    /**
     * @return Mage_Core_Model_Session
     */
    public function getSession()
    {
        return Mage::getSingleton('core/session');
    }

    public function getSessionCountry()
    {
        return $this->getSession()->getCountry();
    }

    public function getSites()
    {
        if ($this->_sites === NULL) {
            $ipConfig = Mage::getStoreConfig('ip');
            $sites = array();
            foreach (array('site1', 'site2', 'site3') as $site) {
                // Skip empty profiles
                if (!isset($ipConfig[$site]['url']) || !isset($ipConfig[$site]['countries']) ||
                    !$ipConfig[$site]['url'] || !$ipConfig[$site]['countries']
                ) continue;
                $sites[$site] = $ipConfig[$site];
            }
            $this->_sites = $sites;
        }
        return $this->_sites;
    }

    public function setSessionCountry($country)
    {
        return $this->getSession()->setCountry($country);
    }

    public function showPopUp($show = NULL)
    {
        if ($show === NULL) return $this->_showPopUp;
        return $this->_showPopUp = Mage::getStoreConfig(self::CONFIG_PATH_SHOW_POPUP) && $show;
    }
}
