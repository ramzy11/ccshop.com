<?php

class Gri_Api_BdtController extends Mage_Api_Controller_Action
{
    /**
     * Access like http://magento.ce/index.php/api/bdt/rest
     */
    public function restAction()
    {
        /* gri_api_bdt_rest => HANDLER from api.xml */
        $this->_getServer()->init($this, 'gri_api_bdt_rest', NULL)
            ->run();
    }
}
