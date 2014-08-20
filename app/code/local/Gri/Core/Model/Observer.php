<?php

class Gri_Core_Model_Observer extends Varien_Object
{
    /**
     * @return Gri_Core_Helper_Ip
     */
    protected function _getIpHelper()
    {
        return Mage::helper('gri_core/ip');
    }

    /**
     * Get singleton instance of the IP model
     * @return Gri_Core_Model_Ip
     */
    protected function _getIpSingleton()
    {
        return Mage::getSingleton('gri_core/ip');
    }

    public function changeCurrency(Varien_Event_Observer $observer)
    {
        if ($this->_getIpHelper()->enableSwitchCurrency()) {
            $request = Mage::app()->getRequest();
            $country = $this->_getIpSingleton()->ipToCountry($request->getClientIp());
            $sessionCountry = $this->_getIpHelper()->getSessionCountry();
            $sessionCountry and $country = $sessionCountry;
            if ($request->getParam('country') || $request->getCookie('country')) {
                $country = $request->getParam('country');
                $country or $country = $request->getCookie('country');
                if (isset($_COOKIE['country'])) {
                    Mage::app()->getCookie()->delete('country');
                }
            }
            $canChange = $sessionCountry != $country;
            $allowedCountries = array_flip(explode(',', Mage::getStoreConfig('general/country/allow')));
            if ($canChange && isset($allowedCountries[$country])) {
                $currencyGroups = $this->_getIpHelper()->getCurrencyGroups();
                $defaultCurrency = Mage::app()->getStore()->getDefaultCurrencyCode();
                $currency = isset($currencyGroups[$country]) ? $currencyGroups[$country] : $defaultCurrency;
                in_array($currency, Mage::app()->getStore()->getAvailableCurrencyCodes()) or $currency = $defaultCurrency;
                Mage::app()->getStore()->setCurrentCurrencyCode($currency);
                $this->_getIpHelper()->setSessionCountry($country);
            }
        }
    }

    public function ipBasedRedirection(Varien_Event_Observer $observer)
    {
        $request = Mage::app()->getRequest();
        // Skip on demand
        $request->getParam('skipIpRedirection') and Mage::app()->getCookie()->set('skipIpRedirection', 1, TRUE);
        if (!$this->_getIpHelper()->enabled() ||
            $this->_getIpHelper()->getSession()->getSkipIpRedirection() ||
            $request->getCookie('skipIpRedirection') ||
            $request->getParam('skipIpRedirection'))
            return;
        /* @var $controller Mage_Core_Controller_Front_Action */
        $controller = $observer->getEvent()->getControllerAction();
        // Skip admin controllers
        if (!$controller instanceof Mage_Core_Controller_Front_Action) return;
		//Skip China Office Checking
		if($this->_getIpSingleton()->isFromChinaOffice($request->getClientIp())) return;
        // Skip API and GRI Core controllers
        if ($controller instanceof Mage_Api_Controller_Action ||
            $controller instanceof Gri_Core_Controller_Abstract) return;
        $ipCountry = $this->_getIpSingleton()->ipToCountry($request->getClientIp());
        // Skip allowed countries
	
        /* force Japanese IP Redirection*/
        if($ipCountry == 'JP')
        {
            $controller->getResponse()->setRedirect('http://www.centralcentralshop.jp')->sendResponse();
            exit;
        }

        $allowedCountries = array_flip(explode(',', Mage::getStoreConfig('general/country/allow')));
        if (isset($allowedCountries[$ipCountry])) return;
        $this->_getIpHelper()->showPopUp(!$this->_getIpHelper()->getSessionCountry());

        // Auto redirect
        if (!$this->_getIpHelper()->autoRedirect()) return;
        // Skip unknown IPs
        if ($ipCountry == 'ZZ') return;
        foreach ($this->_getIpHelper()->getSites() as $site) {
            // Redirect to proper site
            if (($profileCountries = array_flip(explode(',', $site['countries']))) &&
                isset($profileCountries[$ipCountry])
            ) {
                $controller->getResponse()->setRedirect($site['url'])->sendResponse();
                exit;
            }
        }
    }

    public function removeStoreCookie(Varien_Event_Observer $observer)
    {
        if (isset($_COOKIE['___store'])) {
            Mage::app()->getCookie()->delete('___store');
        }
    }
}
