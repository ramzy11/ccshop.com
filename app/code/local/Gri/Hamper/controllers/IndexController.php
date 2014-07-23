<?php

class Gri_Hamper_IndexController extends Mage_Core_Controller_Front_Action
{

    public function indexAction()
    {
        $this->_title('Hamper', TRUE);
        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('checkout/session');
        $this->renderLayout();
        return $this;
    }
}
