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
class Inchoo_Weibo_Customer_AccountController extends Mage_Core_Controller_Front_Action {

    protected $_logFile = 'weibo_login.log';

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

    public function connectAction() {
        $code = $this->getRequest()->getParam('code', false);
        $client = $this->_getWeiboSession()->getClient();

        $accessTokenResponse = $client->getAccessToken($code);
        $access_token = $accessTokenResponse['access_token'];

        //get weibo uid
        $accessTokenInfo = $client->getTokenInfo($access_token);
        $uid = $accessTokenInfo['uid'];

        // relation weibo uid
        if (isset($uid) && !empty($uid) && Mage::app()->getRequest()->getCookie('_gri', false)) {
            $this->updateWeiboId($uid);
            Mage::getsingleton('core/cookie')->set('_gri', '', 0, '/');
            exit('<script> opener.location.href=opener.location.href; window.close() ;</script>');
        }

        // 初始化cookie过期
        $client->delCookie(0);

        //Weibo logined
        if ($this->_getCustomerSession()->isLoggedIn()) {
            $this->_loginPostRedirect();

            exit('<script> opener.location.href=opener.location.href; window.close() ;</script>');
            return;
        } else {
            //weibo customermysql 
            $customer = $this->getCustomerByWeiboUid($uid);
            if ($customer->getId() && !$customer->getConfirmation()) {
                //setcookie
                $this->_getCustomerSession()->setCustomerAsLoggedIn($customer);
                $client->delCookie(0);
                // $this->_loginPostRedirect();
                exit("<script>opener.location.href='" . Mage::getUrl('customer/account') . "'" . '; window.close() ;</script>');
            }

            //weibo  uid  write it to cookie
            $refer = '*/*/*';
            $payload = Zend_Json::encode(array('weibo_uid' => $uid, 'wb-referer' => Mage::getUrl($refer)));
            $cookie_v = $this->_getWeiboSession()->generalSignature($payload) . '.' . $payload;
            $client->setCookie($cookie_v, 300);
        }

        exit("<script>opener.location.href='" . Mage::getUrl('inchoo_weibo/customer_account/rebind') . "'" . '; window.close() ;</script>');
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

    /**
     *   post 
     */
    public function rebindPostAction() {
        $session = $this->_getCustomerSession();
        $weiboSession = $this->_getWeiboSession();

        if ($this->_getCustomerSession()->isLoggedIn()) {
            $this->_redirect('customer/account');
            return;
        }

        $weibo_uid = $weiboSession->getWeiboUid('weibo_uid');
        if (!$weibo_uid) {
            $this->log($this->__('Weibo Uid Not OAuthed'));
            $session->addError($this->__('Weibo Uid Not OAuthed'));
        }

        $session->setEscapeMessages(true); // prevent XSS injection in user input
        if ($this->getRequest()->isPost()) {
            $errors = array();
            if (!$customer = Mage::registry('current_customer')) {
                $customer = Mage::getModel('customer/customer')->setId(null);
            }

            $weibo_uid && $customer->setWeiboUid($weibo_uid);

            /* @var $customerForm Mage_Customer_Model_Form */
            $customerForm = Mage::getModel('customer/form');
            $customerForm->setFormCode('customer_account_create')
                    ->setEntity($customer);

            $customerData = $customerForm->extractData($this->getRequest());
            if ($this->getRequest()->getParam('is_subscribed', false)) {
                $customer->setIsSubscribed(1);
            }

            /**
             * Initialize customer group id
             */
            $customer->getGroupId();

            try {
                $customerErrors = $customerForm->validateData($customerData);
                if ($customerErrors !== true) {
                    $errors = array_merge($customerErrors, $errors);
                } else {
                    $customerForm->compactData($customerData);
                    $customer->setPassword($this->getRequest()->getPost('password'));
                    $customer->setConfirmation($this->getRequest()->getPost('confirmation'));
                    $customerErrors = $customer->validate();
                    if (is_array($customerErrors)) {
                        $errors = array_merge($customerErrors, $errors);
                    }
                }

                $validationResult = count($errors) == 0;
                if (true === $validationResult) {
                    $customer->save();
                    Mage::dispatchEvent('customer_register_success', array('account_controller' => $this, 'customer' => $customer));

                    // email valid
                    if ($customer->isConfirmationRequired()) {
                        $customer->sendNewAccountEmail(
                                'confirmation', $session->getBeforeAuthUrl(), Mage::app()->getStore()->getId()
                        );
                        $session->addSuccess($this->__('必须要账户确认信息 . 请点击邮件中的确认链接. 如果需要重新发送邮件，请<a href="%s">点击</a>这里', Mage::helper('customer')->getEmailConfirmationUrl($customer->getEmail())));
                        $this->_redirectSuccess(Mage::getUrl('/', array('_secure' => true)));
                        return;
                    } else {
                        Mage::getSingleton('customer/session')->setCustomerAsLoggedIn($customer);
                        $url = $this->_welcomeCustomer($customer, true);
                        $this->_redirectSuccess($url);
                        return;
                    }
                } else {
                    $session->setCustomerFormData($this->getRequest()->getPost());
                    if (is_array($errors)) {
                        foreach ($errors as $errorMessage) {
                            $session->addError($errorMessage);
                        }
                    } else {
                        $session->addError($this->__('Invalid customer data'));
                    }
                }
            } catch (Mage_Core_Exception $e) {
                $session->setCustomerFormData($this->getRequest()->getPost());
                if ($e->getCode() === Mage_Customer_Model_Customer::EXCEPTION_EMAIL_EXISTS) {
                    $url = Mage::getUrl('customer/account/edit');
                    $message = $this->__('The email has already exsited, please login it into the system using your current email address and associate your weibo account with the email');
                    $session->setEscapeMessages(false);
                } else {
                    $message = $e->getMessage();
                }
                $session->addError($message);
            } catch (Exception $e) {
                $session->setCustomerFormData($this->getRequest()->getPost())
                        ->addException($e, $this->__('.mk'));
            }
        }

        $this->_redirectError(Mage::getUrl('*/*/connect', array('_secure' => true)));
    }

    /**
     * Add welcome message and send new account email.
     * Returns success URL
     *
     * @param Mage_Customer_Model_Customer $customer
     * @param bool $isJustConfirmed
     * @return string
     */
    protected function _welcomeCustomer(Mage_Customer_Model_Customer $customer, $isJustConfirmed = false) {
        $this->_getCustomerSession()->addSuccess(
                $this->__('感谢您通过 %s 注册.', Mage::app()->getStore()->getFrontendName())
        );
        
        $customer->sendNewAccountEmail(
            $isJustConfirmed ? 'confirmed' : 'registered', '', Mage::app()->getStore()->getId()
        );
        
        $successUrl = Mage::getUrl('customer/account', array('_secure' => true));
        if ($this->_getCustomerSession()->getBeforeAuthUrl()) {
          $successUrl = $this->_getCustomerSession()->getBeforeAuthUrl(true);
        }
        return $successUrl;
    }

    /**
     * Check whether VAT ID validation is enabled
     *
     * @param Mage_Core_Model_Store|string|int $store
     * @return bool
     */
    protected function _isVatValidationEnabled($store = null) {
        return Mage::helper('customer/address')->isVatValidationEnabled($store);
    }

    /**
     *  Getter  Cutomer  
     * 
     *  @param $weibo_uid
     *  
     *  @return  
     */
    protected function getCustomerByWeiboUid($weibo_uid) {
        $existingCustomer = '';
        $customer = Mage::getModel('customer/customer');
        $collection = $customer->getCollection()
                ->addAttributeToFilter('weibo_uid', $weibo_uid)
                ->setPageSize(1);
        $existingCustomer = $collection->getFirstItem();

        //return 
        return $existingCustomer;
    }

    /**
     *  test Action
     */
    public function rebindAction() {
        if (!$this->_getCustomerSession()->isLoggedIn()) {
            // rebind layout 
            $this->loadLayout();
            $this->_initLayoutMessages('customer/session');
            $this->renderLayout();
        } else {
            $this->_redirectUrl(Mage::getUrl('customer/account'));
        }
        
        return;
    }

    /**
     *  rebind weibo uid  in account page  
     *  
     */
    public function plantWeiboUidFlagAction() {
        //plant cookie
        Mage::getsingleton('core/cookie')->set('_gri', '1', '300', '/');
        $this->_redirectUrl('https://api.weibo.com/oauth2/authorize?client_id=' . Mage::getSingleton('inchoo_weibo/config')->getApiKey() . '&display=popup&response_type=code&redirect_uri=' . Mage::getSingleton('inchoo_weibo/config')->getRedirectUri());
    }

    /**
     *  update Weibo  Uid
     *  
     */
    protected function updateWeiboId($weibo_uid) {
        if (intval($weibo_uid) > 0) {
            $customer = $this->getCustomer();
            if ($this->getCustomerByWeiboUid($weibo_uid)->getId()) {
                $this->_getCustomerSession()->addNotice('微博ID: '. $weibo_uid . '已经存在');
                return;
            } else {
                $customer->setWeiboUid($weibo_uid);
                $customer->save();
                return true;
            }
        } else {
            return false;
        }
    }

    /**
     * 
     * @return Mage_Customer_Model_Customer
     */
    protected function getCustomer() {
        return $this->_getCustomerSession()->getCustomer();
    }

    /**
     *  Get Customer Session
     *  
     *  @return Mage_Customer_Model_Session 
     */
    protected function _getCustomerSession() {
        return Mage::getSingleton('customer/session');
    }

    /**
     *   @return  Inchoo_Weibo_Model_Session
     */
    protected function _getWeiboSession() {
        return Mage::getSingleton('inchoo_weibo/session');
    }

    /**
     *  @param $message
     */
    protected function log($message) {
        Mage::getSingleton('inchoo_weibo/client')->log($message);
        return;
    }

    /**
     *  cancel bind weibo
     */
    public function unbindAction() {
        $success = true;
        $message = '';

        $customer = $this->_getCustomerSession()->getCustomer();
        if (!$customer->getId()) {
            $success = false;
            $message = $this->__('unlogined');
        }

        $weibo_uid = $customer->load(null)->getWeiboUid();
        if ($success == true && !empty($weibo_uid)) {
            $customer->setWeiboUid('');
            $customer->save();
        } else {
            $success = false;
            $message = $this->__('Unbind Failure');
        }

         // outpu json
        $data = array('success' => $success, 'message' => $message);
        echo Zend_Json::encode($data);
    }
    
    /**
     * get Weibo Refer 
     */
    protected function getWeiboRefer(){
        $refer =  $this->getRequest()->getCookie('wb-referer');  
    }
        
    protected  function setWeiboRefer(){}

}