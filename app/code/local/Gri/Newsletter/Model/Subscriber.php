<?php

/**
 * @method boolean getCheckSubscribed() Get if check subscribed
 * @method Gri_Newsletter_Model_Subscriber setCheckSubscribed(boolean $check) Set if check subscribed
 */
class Gri_Newsletter_Model_Subscriber extends Mage_Newsletter_Model_Subscriber
{

    public function loadByEmail($subscriberEmail)
    {
        parent::loadByEmail($subscriberEmail);
        if ($this->getCheckSubscribed() && $this->isSubscribed()) {
            Mage::throwException(Mage::helper('gri_message')->__('This email address has been subscribed.'));
        }
        return $this;
    }

    public function subscribe($email)
    {
        $this->setCheckSubscribed(TRUE);
        return parent::subscribe($email);
    }
	
    public function setIsStatusChanged($value)
    {
        $this->_isStatusChanged = (boolean) $value;
		if($value == true)
		{
			$this->setChangeStatusAt(date('Y-m-d H:i:s'));
		}
        return $this;
    }
	
    /**
     * Unsubscribes loaded subscription
     *
     */
    public function unsubscribe()
    {
        if ($this->hasCheckCode() && $this->getCode() != $this->getCheckCode()) {
            Mage::throwException(Mage::helper('newsletter')->__('Invalid subscription confirmation code.'));
        }
		
		$status = $this->getSubscriberStatus();

		if($status != self::STATUS_UNSUBSCRIBED)
		{
			$this->setIsStatusChanged(true);
		}
		
        $this->setSubscriberStatus(self::STATUS_UNSUBSCRIBED)
            ->save();
        $this->sendUnsubscriptionEmail();
        return $this;
    }

   public function sendUnsubscriptionEmail()
    {
        if ($this->getImportMode()) {
            return $this;
        }
        if(!Mage::getStoreConfig(self::XML_PATH_UNSUBSCRIBE_EMAIL_TEMPLATE)
            || !Mage::getStoreConfig(self::XML_PATH_UNSUBSCRIBE_EMAIL_IDENTITY)
        ) {
            return $this;
        }

        $translate = Mage::getSingleton('core/translate');
        /* @var $translate Mage_Core_Model_Translate */
        $translate->setTranslateInline(false);

        $email = Mage::getModel('core/email_template');


        $email->sendTransactional(
            Mage::getStoreConfig(self::XML_PATH_UNSUBSCRIBE_EMAIL_TEMPLATE),
            Mage::getStoreConfig(self::XML_PATH_UNSUBSCRIBE_EMAIL_IDENTITY),
            $this->getEmail(),
            $this->getName(),
            array(
                'subscriber'=>$this,
                'customer_name'=>$this->getSubscriberFullName()?$this->getSubscriberFullName():Mage::helper('newsletter')->__("Customer"),
            )
        );

        $translate->setTranslateInline(true);

        return $this;
    }
}
