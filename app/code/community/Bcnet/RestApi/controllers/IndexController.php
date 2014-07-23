<?php
define('MAGENTO_PLUGIN_VERSION', 'v1.2.3');
class Bcnet_RestApi_IndexController extends Bcnet_RestApi_Controller_Action {
    const CONFIG_PATH_ENABLED = 'bcnetrestapi/active';
    const CONFIG_PATH_APP_KEY = 'Bcnet/Bcnet_group/Bcnet_appkey';
    const CONFIG_PATH_APP_SECRET = 'Bcnet/Bcnet_group/Bcnet_appsecret';

    public function indexAction()
    {
        /* @var Gri_Core_Model_Locale $locale */
        $locale = Mage::app()->getLocale();
        $locale->date()->setTimezone('UTC');

        $request = $this->getRequest();
        $result = Mage::getModel('restapi/Result');

        if(!Mage::getStoreConfig(self::CONFIG_PATH_ENABLED)){
            $result->setResult('0x0001');
            $this->setResponseBody($result->returnResult());
            return;
        }
        if (!$request->getParam('app_key') || $request->getParam('app_key') != Mage::getStoreConfig(self::CONFIG_PATH_APP_KEY)) {
            $result->setResult('0x0019');
            $this->setResponseBody($result->returnResult());
            return;
        }
        if (!$request->getParam('timestamp') || abs($locale->date()->getTimestamp() - strtotime($request->getParam('timestamp'))) > 600) {
            $result->setResult('0x0003');
            $this->setResponseBody($result->returnResult());
            return;
        }
        if (!$request->getParam('sign_method') || $request->getParam('sign_method') != 'md5') {
            $result->setResult('0x0006');
            $this->setResponseBody($result->returnResult());
            return;
        }
        if (!$request->getParam('sign') || !$this->validateRequestSign($request->getParams())) {
            $result->setResult('0x0016');
            $this->setResponseBody($result->returnResult());
            return;
        }

        $session = Mage::getSingleton('customer/session');
        $session = Mage::getSingleton('checkout/session');

        switch ($request->getParam('method')) {
            case 'User.Login' : $returnData = $this->_getApiUser()->Login($request->getParams());
                break;
            case 'User.FacebookLogin' : $returnData = $this->_getApiUser()->FacebookLogin($request->getParams());
                break;
            case 'User.Get' : $returnData = $this->_getApiUser()->Get($request->getParams());
                break;
            case 'User.Register' : $returnData = $this->_getApiUser()->Register($request->getParams());
                break;
            case 'User.Update' : $returnData = $this->_getApiUser()->Update($request->getParams());
                break;
            case 'User.Logout' : $returnData = $this->_getApiUser()->Logout();
                break;
            case 'User.IsExists' : $returnData = $this->_getApiUser()->IsExists($request->getParams());
                break;
            case 'User.ForgotPwd' : $returnData = $this->_getApiUser()->ForgotPwd($request->getParams());
                break;
            case 'User.Subscribe' : $returnData = $this->_getApiUser()->Subscribe($request->getParams());
                break;
            case 'User.Address.Update' : $returnData = $this->_getApiUser()->AddressSave($request->getParams());
                break;
            case 'User.Address.Remove' : $returnData = $this->_getApiUser()->AddressRemove($request->getParams());
                break;
            case 'User.Address.Add' : $returnData = $this->_getApiUser()->AddressSave($request->getParams());
                break;
            case 'User.Addresses.Get' : $returnData = $this->_getApiUser()->AddressesGet($request->getParams());
                break;
            case 'User.Address.Get' : $returnData = $this->_getApiUser()->AddressGet($request->getParams());
                break;

            default: $result->setResult('0x0018');
                $returnData = $result->returnResult();
        }

        $this->setResponseBody($returnData);
    }

    protected function setResponseBody($returnData)
    {
        Mage::log('Request: '.var_export($this->getRequest()->getParams(),true), 7, 'api.log');
        Mage::log('Response: '.var_export($returnData, true), 7, 'api.log');
        $this->getResponse()->setBody(Zend_Json::encode($returnData));
    }

    protected function isEmptyString($string)
    {
        return is_string($string) && empty($string) ? true :false;
    }

    /**
     * @return Bcnet_RestApi_Model_User
     */
    protected function _getApiUser()
    {
        return Mage::getSingleton('restapi/User');
    }

    protected function validateRequestSign(array $requestParams)
    {
        if (!isset($requestParams['sign']) || $this->isEmptyString($requestParams['sign'])) {
            return false;
        }

        $sign = $requestParams['sign'];
        unset($requestParams['sign']);
        ksort($requestParams);
        reset($requestParams);
        $tempStr = "";

        foreach ($requestParams as $key => $value) {
            $tempStr = $tempStr . $key . $value;
        }
        $tempStr = $tempStr .  Mage::getStoreConfig(self::CONFIG_PATH_APP_SECRET);
        return strtolower(md5($tempStr)) === strtolower($sign);
    }
}