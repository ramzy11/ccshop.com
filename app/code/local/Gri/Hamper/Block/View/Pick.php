<?php

class Gri_Hamper_Block_View_Pick extends Gri_Hamper_Block_View
{

    protected function _construct()
    {
        Mage_Catalog_Block_Product_Abstract::_construct();
    }

    public function getMessageBody()
    {
        return $this->getProduct()->getPreconfiguredValues()->getData('hamper_message', 'message');
    }

    public function getMessageFrom()
    {
        return $this->getProduct()->getPreconfiguredValues()->getData('hamper_message', 'from');
    }

    public function getMessageTo()
    {
        return $this->getProduct()->getPreconfiguredValues()->getData('hamper_message', 'to');
    }
}
