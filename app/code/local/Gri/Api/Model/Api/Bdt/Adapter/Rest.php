<?php

class Gri_Api_Model_Api_Bdt_Adapter_Rest extends Inchoo_Api_Model_Api_Server_Adapter_Json
{
    protected $_errors = array();
    protected $_faults;
    
     protected $_logFile = 'api.bdt.server.success.log';

    public function addError($code, $message = NULL)
    {
        if (isset($this->_faults[$code]) && is_array($fault = $this->_faults[$code])) {
            $code = $fault['code'];
            $message === NULL and $message = $fault['message'];
        }
        $this->_errors[] = array(
            'code' => $code,
            'message' => $message,
        );
        return $this;
    }

    public function getErrors()
    {
        return $this->_errors;
    }

    public function getFaults()
    {
        return $this->_faults;
    }

    public function getJson()
    {
        return $this->_json;
    }

    public function run()
    {
        $apiConfigCharset = Mage::getStoreConfig('api/config/charset');
        /* @var $request Gri_Api_Model_Api_Request */
        $request = Mage::getModel('gri_api/api_request');
        $request->setId('bdt');

        $this->_json = new Zend_Json_Server();

        $this->_json->setClass($this->getHandler())->setAutoEmitResponse(FALSE);

        $this->getController()->getResponse()
            ->clearHeaders()
            ->setHeader('Content-Type', 'application/json; charset=' . $apiConfigCharset);
        $result = Zend_Json::decode($this->_json->handle($request));
        $result['data'] = $result['result'];
        $result['errors'] = array();
        $result['errors'] = $this->getErrors();
        $result['error'] and $result['errors'][] = $result['error'];
        unset($result['id'], $result['error']);
        if ($request->getReturn() != 'result') unset($result['result']);

        if (Mage::helper('gri_api')->isDebugMode()) {
            Mage::log('Response JSON: ' .Zend_Json::encode($result), Zend_Log::DEBUG, $this->_logFile);
        }
        
        $this->getController()->getResponse()->setBody(Zend_Json::encode($result));
        return $this;
    }

    public function setFaults($faults)
    {
        $this->_faults = $faults;
        return $this;
    }
}
