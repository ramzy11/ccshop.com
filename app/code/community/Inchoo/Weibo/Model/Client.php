<?php

/**
 * Weibo Graph/Rest client
 *
 * @category    Inchoo
 * @package     Inchoo_Weibo
 * @author      Ivan Weiler <ivan.weiler@gmail.com>
 * @copyright   Inchoo (http://inchoo.net)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Inchoo_Weibo_Model_Client {

    const WEIBO_ACCESS_TOKEN_URI = 'https://api.weibo.com/oauth2/access_token';
    const WEIBO_TOKEN_INFO_URI = 'https://api.weibo.com/oauth2/get_token_info';
    const WEIBO_PROFILE_BASIC_URI = 'https://api.weibo.com/2/account/profile/basic.json';
    
    protected $_logFile = 'weibo_login.log';
    protected $_apiKey;
    protected $_secret;
    protected $_redirectUri;
    protected $_session;
    protected $_accessToken;
    protected static $_httpClient;

    public function __construct() {
        $args = func_get_args();

        $args = array($args[0][0], $args[0][1], $args[0][2], $args[0][3]);

        if (count($args) < 4) {
            trigger_error('Missing arguments for Inchoo_Weibo_Model_Client::__construct()', E_USER_ERROR);
            return ;
        }

        if (isset($args[0]) && is_array($args[0])) {
            $args = $args[0];
        }

        $this->_apiKey = $args[0];
        $this->_secret = $args[1];
        $this->_redirectUri = $args[2];
        $session = isset($args[3]) ? $args[3] : null;

        if (is_array($session)) {
            $this->_session = new Varien_Object($session);
        } elseif ($session instanceof Varien_Object) {
            $this->_session = $session;
        } else {
            $this->_session = new Varien_Object();
        }
    }

    public function setSession($session) {
        $this->_session = $session;
        return $this;
    }

    public function setAccessToken($accessToken) {
        $this->_accessToken = $accessToken;
        return $this;
    }

    public function call(/* polymorphic */){ }

    public function restBatch($batch_queue) {
        $method_feed = array();
        foreach ($batch_queue as $call) {
            $p = $this->_prepareParams($call);
            $method_feed[] = http_build_query($p, '', '&');
        }

        $params = array(
            'method_feed' => json_encode($method_feed),
            'serial_only' => true
        );

        return $this->rest('batch.run', $params);
    }

    protected function _getAccessToken() {
        if ($this->_accessToken) {
            return $this->_accessToken;
        }
        
        try {
            $accessTokenResponse = self::_getHttpClient()
                    ->setUri(self::WEIBO_ACCESS_TOKEN_URI)
                    ->setMethod(Zend_Http_Client::GET)
                    ->resetParameters()
                    ->setParameterPost($this->_prepareParams(array(
                                'client_id' => $this->_apiKey,
                                'client_secret' => $this->_secret,
                                'redirect_uri' => '',
                                'code' => $this->_session->getCode(),
                            )))
                    ->request()
                    ->getBody();

            $responseParams = array();
            parse_str($accessTokenResponse, $responseParams);
            if (isset($responseParams['access_token'])) {
                $this->_accessToken = $responseParams['access_token'];
            }
        } catch (Exception $e) {  }

        if (!$this->_accessToken) {
            $this->_accessToken = $this->_apiKey . '|' . $this->_secret;
        }

        return $this->_accessToken;
    }

    protected function _prepareParams($params) {
        foreach ($params as $key => &$val) {
            if (!is_array($val))
                continue;
            $val = Zend_Json::encode($val);
        }

        return $params;
    }

    protected function _oauthRequest($url, $params) {
        if (!isset($params['access_token'])) {
            $params['access_token'] = $this->_getAccessToken();
        }
        $params = $this->_prepareParams($params);

        $client = self::_getHttpClient()
                ->setUri($url)
                ->setMethod(Zend_Http_Client::POST)
                ->resetParameters()
                ->setParameterPost($params);
        try {
            $response = $client->request();
        } catch (Exception $e) {
            throw new Mage_Core_Exception('Service temporarily unavailable.');
        }
        $result = Zend_Json::decode($response->getBody());

        return $result;
    }

    private static function _getHttpClient() {
        if (!self::$_httpClient instanceof Varien_Http_Client) {
            self::$_httpClient = new Varien_Http_Client();
        }
        return self::$_httpClient;
    }

    private function _isReadOnlyMethod($method) {
        return in_array(strtolower($method), array());
    }

    /**
     *  get token info : 
     *  {"uid": 1073880650, "appkey": 1352222456,"scope": null,
     *   "create_at": 1352267591,"expires_in": 157679471
     *   } 
     *   @param $code
     *   
     *   @return  mixed | json
     */
    public function getAccessToken($code) {
        $response = self::_getHttpClient()
                ->setUri(self::WEIBO_ACCESS_TOKEN_URI)
                ->setMethod(Zend_Http_Client::POST)
                ->resetParameters()
                ->setParameterPost($this->_prepareParams(array(
                            'client_id' => $this->_apiKey,
                            'client_secret' => $this->_secret,
                            'grant_type' => 'authorization_code',
                            'code' => $code,
                            'redirect_uri' => Mage::getSingleton('inchoo_weibo/config')->getRedirectUri()
                        )))
                ->request()
                ->getBody();

        $response = Zend_Json::decode($response);
        if (isset($response['error_code']) && $response['error_code']) {
            $this->_addWarning($response);
            return;
        }
        
        return $response;
    }

    public function getTokenInfo($access_token) {
        $response = self::_getHttpClient()
                ->setUri(self::WEIBO_TOKEN_INFO_URI)
                ->setMethod(Zend_Http_Client::POST)
                ->resetParameters()
                ->setParameterPost($this->_prepareParams(array(
                            'access_token' => $access_token
                        )))
                ->request()
                ->getBody();

        $response = Zend_Json::decode($response);
        if (isset($response['error_code']) && $response['error_code']) {
            $this->_addWarning($response);
            return;
        }

        return $response;
    }

    /**
     *  Get account  info
     *  
     *  @param  striong $access_token
     *  @param  int   $uid 
     *  
     *  @return  obj
     */
    public function getAccountInfo($access_token, $uid) {
        $response = self::_getHttpClient()
                ->setUri(self::WEIBO_PROFILE_BASIC_URI)
                ->setMethod(Zend_Http_Client::GET)
                ->resetParameters()
                ->setParameterPost($this->_prepareParams(array(
                            'access_token' => $access_token,
                            'uid' => $uid
                        )))
                ->request()
                ->getBody();

        $response = Zend_Json::decode($response);
        if (isset($response['error_code']) && $response['error_code']) {
            $this->_addWarning($response);
            return;
        }

        return $response;
    }

    public function _addWarning($response) {
        Mage::getSingleton('customer/session')->addWarning($response);
    }

    /**
     *  @param  $uid 
     *  @param  $lifetime
     *  @param  $encodedSignature
     *  @param  $refer   
     *  
     *  @return  
     */
    public function setCookie($cookie_v, $lifetime = '43200') {
        Mage::getsingleton('core/cookie')->set('wbsr_' . Mage::getSingleton('inchoo_weibo/config')->getApiKey(),$cookie_v, $lifetime, '/');
        return ;       
    }
    
    /**
     *  
     *  @return mixed
     */
    public  function  delCookie($lifetime = '0'){
        return  Mage::getsingleton('core/cookie')->set('wbsr_' . Mage::getSingleton('inchoo_weibo/config')->getApiKey(),'', $lifetime, '/');   
    }
    
    
    /**
     *  weibo apiKey
     *  
     */
    public function getCookie() {
        return Mage::app()->getRequest()->getCookie('wbsr_' . Mage::getSingleton('inchoo_weibo/config')->getApiKey(), false);
    }
    
   /**
    *  @param $message
    *   
    */
    public  function  log($message){
        Mage::log($message,Zend_Log::WARN , $this->_logFile);
        return ;
    }
}