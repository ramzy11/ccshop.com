<?php

class Gri_Core_Block_ShipsTo extends Mage_Core_Block_Template
{
    protected $_countryToCurrency = array(
        'HK' => 'HKD',
        'MO' => 'HKD',
        'MY' => 'USD',
        'SG' => 'USD',
        'TW' => 'USD',
        'TH' => 'USD',
        'CN' => 'CNY'
    );

    /**
     * @return Gri_Core_Helper_Ip
     */
    protected function _getIpHelper()
    {
        return $this->helper('gri_core/ip');
    }

    public function getAllowedCountries()
    {
        $this->hasData($key = 'allowed_countries') OR
            $this->setData($key, explode(',', Mage::getStoreConfig('general/country/allow')));
        return $this->_getData($key);
    }

    public function getAllowedShipsToCountry()
    {
        if ($this->getData('allowed_ships_to_country') === NULL) {
            $ipCountry = $this->_getIpHelper()->getSessionCountry();
            $ipCountry or $ipCountry = $this->getIpSingleton()->ipToCountry($this->getRequest()->getClientIp());
            // Unknown IPs
            if ($ipCountry == 'ZZ') $this->setData('allowed_ships_to_country', $this->getDefaultCountry());
            else {
                $allowedCountries = array_flip($this->getAllowedCountries());
                $this->setData('allowed_ships_to_country',  isset($allowedCountries[$ipCountry]) ?
                    // Allowed countries
                    $ipCountry :
                    // Disallowed countries
                    FALSE
                );
            }
        }
        return $this->getData('allowed_ships_to_country');
    }

    public function getCountryNameByCode($code)
    {
        return Mage::app()->getLocale()->getCountryTranslation($code);
    }

    public function getCurrencySymbol()
    {
        $currency = Mage::app()->getStore()->getCurrentCurrency();
        return Mage::helper('gri_directory')->getCurrencySymbol($currency);
    }

    public function getDefaultCountry()
    {
        return Mage::getStoreConfig(Mage_Core_Helper_Data::XML_PATH_DEFAULT_COUNTRY);
    }

    /**
     * Get singleton instance of the IP model
     * @return Gri_Core_Model_Ip
     */
    public function getIpSingleton()
    {
        return Mage::getSingleton('gri_core/ip');
    }

    public function getSiteLandingPage($siteUrl, $country)
    {
        substr($siteUrl, -1) == '/' or $siteUrl .= '/';

        $return = $siteUrl;
        if($country == 'CN')
        {       
                $return = $siteUrl . 'gri_core/ip/land/skipIpRedirection/1/country/' . $country;
        }

        return $return;
    }

    public function getSitesCountries()
    {
        if ($this->_getData('sites_countries') === NULL) {
            $countries = array();
            foreach ($this->_getIpHelper()->getSites() as $site) {
                $siteCountries = explode(',', $site['countries']);
                foreach ($siteCountries as $country) {
                    $countries[$this->getCountryNameByCode($country)] = $this->getSiteLandingPage($site['url'], $country);
                }
            }
            $this->setData('sites_countries', $countries);
        }
        return $this->_getData('sites_countries');
    }

    public function getStayUrl()
    {
        return $this->getUrl('gri_core/ip/stay');
    }

    public function getSwitcherUrl()
    {
        return $this->_getIpHelper()->enabled() ? '#ships-to-popup' : 'javascript:;';
    }

    public function getCurrencySymbolByCountryCode($countryCode)
    {
        if($countryCode == 'JP')
        {
            return 'JP￥';
        }


		if($countryCode == 'CN')
		{
			return 'CN￥';
		}

        if(isset($this->_countryToCurrency[$countryCode]) && $currencyCode = $this->_countryToCurrency[$countryCode]){
            return $this->_getLocale()->currency($currencyCode)->getSymbol();
        }

        return $this->_getLocale()->currency(Mage::app()->getStore()->getDefaultCurrency())->getSymbol();
    }

    protected function _getLocale()
    {
        return  Mage::app()->getLocale();
    }
}
