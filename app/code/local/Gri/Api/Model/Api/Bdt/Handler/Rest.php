<?php

class Gri_Api_Model_Api_Bdt_Handler_Rest extends Mage_Api_Model_Server_Handler_Abstract
{

    /**
     * @return Gri_Api_Model_Api_Bdt_Adapter_Rest
     */
    protected function _getAdapter()
    {
        return $this->_getServer()->getAdapter();
    }

    public function call($sessionId, $apiPath, $args = array())
    {
        $apiPath = 'bdt.' . $apiPath;
        try {
            return parent::call($sessionId, $apiPath, $args);
        }
        catch (Exception $e)
        {
            $data = NULL;
            isset($_SERVER['MAGE_IS_DEVELOPER_MODE']) && $_SERVER['MAGE_IS_DEVELOPER_MODE'] and $data = $e;
            $error = new Gri_Api_Model_Api_Error($e->getMessage(), $e->getCode(), $data);
            $this->_getAdapter()->getJson()->getResponse()->setError($error);
            return;
        }
    }
}
