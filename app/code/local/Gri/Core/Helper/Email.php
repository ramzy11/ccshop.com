<?php

class Gri_Core_Helper_Email extends Mage_Core_Helper_Abstract
{

    /**
     * @return Gri_Core_Model_Email_Template
     */
    public function getEmailTemplate()
    {
        return Mage::getSingleton('core/email_template');
    }

    public function getLogoAlt()
    {
        return $this->getEmailTemplate()->getLogoAlt(Mage::app()->getStore()->getId());
    }

    public function getLogoUrl()
    {
        return $this->getEmailTemplate()->getLogoUrl(Mage::app()->getStore()->getId());
    }
}