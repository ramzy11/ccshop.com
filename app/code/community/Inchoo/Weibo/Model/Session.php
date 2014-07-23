<?php

/**
 * Weibo session model
 * 
 * @category    Inchoo
 * @package     Inchoo_Weibo
 * @author      Ivan Weiler <ivan.weiler@gmail.com>
 * @copyright   Inchoo (http://inchoo.net)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Inchoo_Weibo_Model_Session extends Mage_Core_Model_Session {

    private $_client;
    private $_payload;
    private $_signature;
    protected $_logFile = 'weibo_login.log';
    
    public function __construct() {
        if ($this->getCookie()) {
            list($encodedSignature, $payload) = explode('.', $this->getCookie(), 2);// wbsr.apiKey
            $this->log('payload: '.$payload);  
            $this->log('signature: '.$encodedSignature);  
            
            //decode data
            //$signature = base64_decode(strtr($encodedSignature, '-_', '+/'));
            //$data = json_decode(base64_decode(strtr($payload, '-_', '+/')), true); // UserId 

            $signature = $encodedSignature ;
            $data = Zend_Json::decode($payload); // UserId 
            $this->setData($data);
            
            //compatibility hack
            $this->setUid((string) $this->getWeiboUid());
                        
            $this->_signature = $signature;
            $this->_payload = $payload;
        }
    }

    public function isConnected() {
        return $this->validate();
    }

    public function validate() {
        if (!$this->hasData()) {
            return false;
        }
        
        $expectedSignature = hash_hmac('sha256', $this->_payload, Mage::getSingleton('inchoo_weibo/config')->getSecret(), true);
        return ($expectedSignature == $this->_signature);
    }
    
    /**
     *  weibo apiKey
     *  
     */
    public function getCookie() {
        return  Mage::getSingleton('inchoo_weibo/client')->getCookie();
        //return Mage::app()->getRequest()->getCookie('wbsr_' . Mage::getSingleton('inchoo_weibo/config')->getApiKey(), false);
    }

    /**
     *  Get Cient
     *  
     */
    public function getClient() {
        if (is_null($this->_client)) {
            $this->_client = Mage::getModel('inchoo_weibo/client', array(
                        Mage::getSingleton('inchoo_weibo/config')->getApiKey(),
                        Mage::getSingleton('inchoo_weibo/config')->getSecret(),
                        Mage::getSingleton('inchoo_weibo/config')->getRedirectUri(),
                        $this
                    ));
        }
        return $this->_client;
    }
       
    /**
     *  Geter  Weibo  Uid
     *  
     */
    public  function getWeiboUid(){
      return  $this->getData('weibo_uid');
    }
    
   /**
    *  general signture
    *  
    *  @return string
    */
    public  function generalSignature($payload){
       return  hash_hmac('sha256', $payload , Mage::getSingleton('inchoo_weibo/config')->getSecret(), true);           
    }
    
    
   /**
    *  @param $message
    *   
    */
    protected   function  log($message){
       Mage::getSingleton('inchoo_weibo/client')->log($message);
       return ;
    }
}