<?php

class Gri_Core_IpController extends Gri_Core_Controller_Abstract
{
    /**
     * @return Gri_Core_Helper_Ip
     */
    protected function _getIpHelper()
    {
        return Mage::helper('gri_core/ip');
    }

    public function landAction()
    {
        $this->getResponse()->setRedirect(Mage::getUrl());
        $this->stayAction();
    }

    public function stayAction()
    {
        $this->_getIpHelper()->getSession()->setSkipIpRedirection(1);
        $this->getResponse()->setBody('1');
    }
}
