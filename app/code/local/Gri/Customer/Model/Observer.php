<?php

class Gri_Customer_Model_Observer extends Mage_Persistent_Model_Observer
{

    /**
     * @return Mage_Customer_Model_Session
     */
    public function getCustomerSession()
    {
        return Mage::getSingleton('customer/session');
    }

    public function emulateCustomer($observer)
    {
        if (!Mage::helper('persistent')->canProcess($observer)
            || !$this->_isShoppingCartPersist()
        ) {
            return $this;
        }

        if ($this->_isLoggedOut()) {
            /* @var $customer Mage_Customer_Model_Customer */
            $customer = Mage::getModel('customer/customer')->load(
                $this->_getPersistentHelper()->getSession()->getCustomerId()
            );
            $customer->getId() and $this->getCustomerSession()->setCustomerAsLoggedIn($customer);
        }
        return $this;
    }

    public function setBeforeAuthUrl(Varien_Event_Observer $observer)
    {
        /* @var $action Mage_Customer_AccountController */
        $action = $observer->getEvent()->getControllerAction();

        /* @var $session Mage_Customer_Model_Session */
        $session = Mage::getSingleton('customer/session');
        if ($url = $action->getRequest()->getParam('before_auth_url')) {
            $session->setBeforeAuthUrl($url);
        }
    }

    public function subscribeOrUnsubscribeCustomer($observer)
    {
        $customer = $observer->getEvent()->getCustomer();
        $subscriber = Mage::getModel('newsletter/subscriber') ;
        $subscriberByCustomer = Mage::getModel('newsletter/subscriber')->load($customer->getId(),'customer_id');
        $isCustomerOwnNewsLetter = $subscriberByCustomer->getStatus() == Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED && $subscriberByCustomer->getSubscriberConfirmCode();
        if (($customer instanceof Gri_Customer_Model_Customer) && strtolower(Mage::app()->getRequest()->getActionName()) == 'editpost' ) {
            if( $is_subscribed = Mage::app()->getRequest()->getParam('is_subscribed')){
                if(!$isCustomerOwnNewsLetter){
                    $subscriber->subscribe($customer->getEmail());
                }
            }else{
                if($isCustomerOwnNewsLetter){
                    $subscriber->load($customer->getEmail(),'subscriber_email')->unsubscribe();
                }
            }
        }
        return $this;
    }
}
