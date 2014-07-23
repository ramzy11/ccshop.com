<?php

class Gri_Message_Block_Messages extends Mage_Core_Block_Messages
{

    public function getGroupedHtml()
    {
        $messages = $this->getMessages();
        /* @var $message Mage_Core_Model_Message_Abstract */
        foreach ($messages as $message) {
            $message->setCode($this->getMessageHelper()->__($message->getCode()));
        }
        return parent::getGroupedHtml();
    }

    /**
     * @return Gri_Message_Helper_Data
     */
    public function getMessageHelper()
    {
        return $this->helper('gri_message');
    }
}
