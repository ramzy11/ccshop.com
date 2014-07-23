<?php

class Gri_Api_Model_Api_Error extends Zend_Json_Server_Error
{

    public function setCode($code)
    {
        $this->_code = (int)$code;
        return $this;
    }
}
