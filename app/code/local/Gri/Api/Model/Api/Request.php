<?php

class Gri_Api_Model_Api_Request extends Zend_Json_Server_Request_Http
{
    protected $_logFile = 'api.hkas400.server.log';
    protected $_return;

    public function __construct()
    {
        parent::__construct();
        if (Mage::helper('gri_api')->isDebugMode()) {
            Mage::log('Raw JSON: ' . $this->getRawJson(), Zend_Log::DEBUG, $this->_logFile);
        }
    }

    /**
     * Retrieve return field
     *
     * @return string
     */
    public function getReturn()
    {
        return $this->_return;
    }

    /**
     * Set return field
     *
     * @param string $name
     * @return Gri_Api_Model_Api_Request
     */
    public function setReturn($name)
    {
        $this->_return = (string) $name;
        return $this;
    }
}
