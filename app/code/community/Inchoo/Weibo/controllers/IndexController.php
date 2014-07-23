<?php

/**
 * Weibo Customer account controller
 *
 * @category    Inchoo
 * @package     Inchoo_Weibo
 * @author      Ivan Weiler <ivan.weiler@gmail.com>
 * @copyright   Inchoo (http://inchoo.net)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Inchoo_Weibo_IndexController extends Mage_Core_Controller_Front_Action {
    const  WEIBO_ACCESS_TOKEN_URI = 'https://api.weibo.com/oauth2/access_token';
    const  WEIBO_PROFILE_BASIC_URI = 'https://api.weibo.com/2/account/profile/basic.json'; 
    const  WEIBO_TOKEN_INFO_URI = 'https://api.weibo.com/oauth2/get_token_info';
    
    public function preDispatch() {
        parent::preDispatch();
        if (!Mage::getSingleton('inchoo_weibo/config')->isEnabled()) {
            $this->norouteAction();
        }
        return $this;
    }
    
    public function postDispatch() {
        parent::postDispatch();
        Mage::app()->getCookie()->delete('wb-referer');
        return $this;
    }
    
    // 
    public function  loginAction(){
       $code = $this->getRequest()->getParam('code',false);
       
       $accessTokenResponse = self::_getHttpClient()
                    ->setUri(self::WEIBO_ACCESS_TOKEN_URI)
                    ->setMethod(Zend_Http_Client::POST)
                    ->resetParameters()
                    ->setParameterPost($this->_prepareParams(array(
                                'client_id' => Mage::getSingleton('inchoo_weibo/config')->getApiKey(),
                                'client_secret' => Mage::getSingleton('inchoo_weibo/config')->getSecret(),
                                'grant_type' => 'authorization_code',
                                'code' => $code,
                                'redirect_uri' => age::getSingleton('inchoo_weibo/config')->getRedirectUri()
                            )))
                    ->request()
                    ->getBody();
       //{ "access_token":"SlAV32hkKG", "remind_in ":3600, "expires_in":3600 } 
       $accessTokenResponse = json_decode( $accessTokenResponse);
       $access_token = $accessTokenResponse['access_token'];
       
       //get uid
       $accessTokenInfo = self::_getHttpClient()
                    ->setUri(self::WEIBO_TOKEN_INFO_URI)
                    ->setMethod(Zend_Http_Client::POST)
                    ->resetParameters()
                    ->setParameterPost($this->_prepareParams(array(
                                'access_token' => $access_token 
                            )))
                    ->request()
                    ->getBody();
       // {"uid": 1073880650, "appkey": 1352222456,"scope": null,"create_at": 1352267591,"expires_in": 157679471}
       $uid =  $accessTokenInfo['uid'];
            
       // get email
       $accountInfo = self::_getHttpClient()
                    ->setUri(self::WEIBO_PROFILE_BASIC_URI)
                    ->setMethod(Zend_Http_Client::GET)
                    ->resetParameters()
                    ->setParameterPost($this->_prepareParams(array(
                                'access_token' => $access_token ,
                                'uid'=> $uid
                            )))
                    ->request()
                    ->getBody();
       $standInfo = array() ; // array
       $standardInfo['email'] = $accountInfo['email'];
       $standardInfo['first_name'] = $accountInfo['screen_name'];
       $standardInfo['last_name'] = $accountInfo['real_name'];
       $standardInfo['gender'] = $accountInfo['gender'];
       $standardInfo['birthday'] = $accountInfo['birthday'];
       
       // 注册weibo login  cookie 信息
       
       
       
       
       
       
       
       //跳转到 connection 
       $direct_url = Mage::getBaseUrl().'?'.http_build_query($standInfo) ;
       $this->_redirectUrl($direct_url);
    }
    
    public function connectAction() {
        if (!$this->_getSession()->validate()) {
            $this->_getCustomerSession()->addError($this->__('Weibo connection failed.'));
            $this->_redirect('customer/account');
            return;
        }

        //login or connect
        $customer = Mage::getModel('customer/customer');

        $collection = $customer->getCollection()
                ->addAttributeToFilter('weibo_uid', $this->_getSession()->getUid())
                ->setPageSize(1);

        if ($customer->getSharingConfig()->isWebsiteScope()) {
            $collection->addAttributeToFilter('website_id', Mage::app()->getWebsite()->getId());
        }

        if ($this->_getCustomerSession()->isLoggedIn()) {
            $collection->addFieldToFilter('entity_id', array('neq' => $this->_getCustomerSession()->getCustomerId()));
        }

        $uidExist = (bool) $collection->count();

        if ($this->_getCustomerSession()->isLoggedIn() && $uidExist) {
            $existingCustomer = $collection->getFirstItem();
            $existingCustomer->setWeiboUid('');
            $existingCustomer->getResource()->saveAttribute($existingCustomer, 'weibo_uid');
        }

        if ($this->_getCustomerSession()->isLoggedIn()) {
            $currentCustomer = $this->_getCustomerSession()->getCustomer();
            $currentCustomer->setWeiboUid($this->_getSession()->getUid());
            $currentCustomer->getResource()->saveAttribute($currentCustomer, 'weibo_uid');

            $this->_getCustomerSession()->addSuccess(
                    $this->__('Your Weibo account has been successfully connected. Now you can fast login using Weibo Connect anytime.')
            );
            $this->_redirect('customer/account');
            return;
        }

        if ($uidExist) {
            $uidCustomer = $collection->getFirstItem();
            if ($uidCustomer->getConfirmation()) {
                $uidCustomer->setConfirmation(null);
                Mage::getResourceModel('customer/customer')->saveAttribute($uidCustomer, 'confirmation');
            }
            $this->_getCustomerSession()->setCustomerAsLoggedIn($uidCustomer);
            //since FB redirects IE differently, it's wrong to use referer like before
            $this->_loginPostRedirect();
            return;
        }
        
        //let's go with an e-mail
        try {
           // $standardInfo = $this->_getSession()->getClient()->call("/me");
           $standardInfo = $this->request()->getParam('standInfo');
            
        } catch (Mage_Core_Exception $e) {
            $this->_getCustomerSession()->addError(
                    $this->__('Weibo connection failed.') .
                    ' ' .
                    $this->__('Service temporarily unavailable.')
            );
            $this->_redirect('customer/account/login');
            return;
        }

        if (!isset($standardInfo['email'])) {
            $this->_getCustomerSession()->addError(
                    $this->__('Weibo connection failed.') .
                    ' ' .
                    $this->__('Email address is required.')
            );
            $this->_redirect('customer/account/login');
            return;
        }

        $customer
                ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                ->loadByEmail($standardInfo['email']);

        if ($customer->getId()) {
            $customer->setWeiboUid($this->_getSession()->getUid());
            Mage::getResourceModel('customer/customer')->saveAttribute($customer, 'weibo_uid');

            if ($customer->getConfirmation()) {
                $customer->setConfirmation(null);
                Mage::getResourceModel('customer/customer')->saveAttribute($customer, 'confirmation');
            }

            $this->_getCustomerSession()->setCustomerAsLoggedIn($customer);
            $this->_getCustomerSession()->addSuccess(
                    $this->__('Your Weibo account has been successfully connected. Now you can fast login using Weibo Connect anytime.')
            );
            $this->_redirect('customer/account');
            return;
        }

        //registration needed
        $randomPassword = $customer->generatePassword(8);
        
        $customer->setId(null)
                ->setSkipConfirmationIfEmail($standardInfo['email'])
                ->setFirstname($standardInfo['first_name'])
                ->setLastname($standardInfo['last_name'])
                ->setEmail($standardInfo['email'])
                ->setPassword($randomPassword)
                ->setConfirmation($randomPassword)
                ->setWeiboUid($this->_getSession()->getUid());
        
        //WB: Show my sex in my profile
        if (isset($standardInfo['gender']) && $gender = Mage::getResourceSingleton('customer/customer')->getAttribute('gender')) {
            $genderOptions = $gender->getSource()->getAllOptions();
            foreach ($genderOptions as $option) {
                if ($option['label'] == ucfirst($standardInfo['gender'])) {
                    $customer->setGender($option['value']);
                    break;
                }
            }
        }

        //FB: Show my full birthday in my profile
        if (isset($standardInfo['birthday']) && count(explode('/', $standardInfo['birthday'])) == 3) {

            $dob = $standardInfo['birthday'];

            if (method_exists($this, '_filterDates')) {
                $filtered = $this->_filterDates(array('dob' => $dob), array('dob'));
                $dob = current($filtered);
            }

            $customer->setDob($dob);
        }

        //$customer->setIsSubscribed(1);
        //registration will fail if tax required, also if dob, gender aren't allowed in profile
        $errors = array();
        $validationCustomer = $customer->validate();
        if (is_array($validationCustomer)) {
            $errors = array_merge($validationCustomer, $errors);
        }
        $validationResult = count($errors) == 0;

        if (true === $validationResult) {
            $customer->save();

            $this->_getCustomerSession()->addSuccess(
                    $this->__('Thank you for registering with %s', Mage::app()->getStore()->getFrontendName()) .
                    '. ' .
                    $this->__('You will receive welcome email with registration info in a moment.')
            );

            $customer->sendNewAccountEmail();

            $this->_getCustomerSession()->setCustomerAsLoggedIn($customer);
            $this->_redirect('customer/account');
            return;

            //else set form data and redirect to registration
        } else {
            $this->_getCustomerSession()->setCustomerFormData($customer->getData());
            $this->_getCustomerSession()->addError($this->__('Weibo profile can\'t provide all required info, please register and then connect with Weibo for fast login.'));
            if (is_array($errors)) {
                foreach ($errors as $errorMessage) {
                    $this->_getCustomerSession()->addError($errorMessage);
                }
            }

            $this->_redirect('customer/account/create');
        }
    }

    protected function _loginPostRedirect() {
        $session = $this->_getCustomerSession();
        $redirectUrl = Mage::getUrl('customer/account');

        if ($session->getBeforeAuthUrl() &&
                !in_array($session->getBeforeAuthUrl(), array(Mage::helper('customer')->getLogoutUrl(), Mage::getBaseUrl()))) {
            $redirectUrl = $session->getBeforeAuthUrl(true);
        } elseif (($referer = $this->getRequest()->getCookie('wb-referer'))) {
            $referer = Mage::helper('core')->urlDecode($referer);

            //@todo: check why is this added in Magento 1.7
            //$referer = Mage::getModel('core/url')->getRebuiltUrl(Mage::helper('core')->urlDecode($referer));

            if ($this->_isUrlInternal($referer)) {
                $redirectUrl = $referer;
            }
        }

        $this->_redirectUrl($redirectUrl);
    }

    private function _getCustomerSession() {
       return Mage::getSingleton('customer/session');
    }

    private function _getSession() {
       return Mage::getSingleton('inchoo_weibo/session');
    }
    
    
   

}
