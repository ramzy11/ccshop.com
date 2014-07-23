<?php

class Gri_Core_Model_Url extends Mage_Core_Model_Url
{
    const CONFIG_PATH_SUBDOMAIN_ENABLED = 'subdomain/general/enabled';
    protected $_brands = array(
        'ninewest' => 'ninewest',
        'stevemadden' => 'stevemadden',
        'betseyjohnson' => 'betseyjohnson',
        'eqiq' => 'eqiq',
        'carolinnaespinosa' => 'carolinnaespinosa',
        'ninewest.html' => 'ninewest',
        'stevemadden.html' => 'stevemadden',
        'betseyjohnson.html' => 'betseyjohnson',
        'eqiq.html' => 'eqiq',
        'carolinnaespinosa.html' => 'carolinnaespinosa',
        'jeannepierre' => 'jeannepierre',
        'jeannepierre.html'=> 'jeannepierre',
    );

    /**
     * Check If a URL Looks Like a File and Then the Trailing Slash Needs to Be Removed
     * Example:
     * http://host/abc/ => FALSE
     * http://host/abc.html/ => TRUE
     * http://host/abc.html => FALSE
     *
     * @param string $url
     * @return boolean
     */
    protected function _checkRemoveTrailingSlash($url)
    {
        if (substr($url, -1) != '/') return FALSE;
        $baseUrl = $this->getBaseUrl();
        $path = explode('/', substr($url, strlen($baseUrl), -1));
        return ($filename = end($path)) && strpos($filename, '.') !== FALSE;
    }

    public function getBrandBaseUrl($brand)
    {
        if ($brandBaseUrl = Mage::getStoreConfig('subdomain/brand/' . $brand)) {
            return $brandBaseUrl;
        }
        $baseUrl = $this->getBaseUrl();
        $url = substr_replace($baseUrl, '://' . $brand . '.', strpos($baseUrl, '://'), strpos($baseUrl, '://www.') ? 7 : 3);
        return $url;
    }

    public function getRouteUrl($routePath = NULL, $routeParams = NULL)
    {
        $url = parent::getRouteUrl($routePath, $routeParams);
        if ($this->_checkRemoveTrailingSlash($url)) $url = substr($url, 0, -1);
        if (Mage::getStoreConfig(self::CONFIG_PATH_SUBDOMAIN_ENABLED)) {
            $baseUrl = $this->getBaseUrl();
            $path = explode('/', substr($url, strlen($baseUrl)));
            if (($brand = array_shift($path)) && isset($this->_brands[$brand])) {
                $brandBaseUrl = $this->getBrandBaseUrl($this->_brands[$brand]);
                $url = $brandBaseUrl . implode('/', $path);
            }
        }
        return $url;
    }
}
