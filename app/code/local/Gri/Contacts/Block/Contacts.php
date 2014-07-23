<?php

class Gri_Contacts_Block_Contacts extends Mage_Core_Block_Template
{
    public function getFormAction()
    {
        return Mage::getUrl('contacts/index/post/');
    }
}