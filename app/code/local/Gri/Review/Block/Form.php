<?php

class Gri_Review_Block_Form extends Mage_Review_Block_Form
{
    public function __construct()
    {
        $customerSession = Mage::getSingleton('customer/session');

        parent::__construct();

        $data =  Mage::getSingleton('review/session')->getFormData(true);
        $data = new Varien_Object($data);

        $customer = $customerSession->getCustomer();
        // add logged in customer name as nickname
        if (!$data->getNickname()) {
            if ($customer && $customer->getId()) {
                $data->setNickname($customer->getFirstname());
            }
        }

        // add logged in customer email
        if (!$data->getEmail()) {
            if ($customer && $customer->getId()) {
                $data->setEmail($customer->getEmail());
            }
        }

        $this->setAllowWriteReviewFlag($customerSession->isLoggedIn() || Mage::helper('review')->getIsGuestAllowToWrite());
        if (!$this->getAllowWriteReviewFlag) {
            $this->setLoginLink(
                Mage::getUrl('customer/account/login/', array(
                        Mage_Customer_Helper_Data::REFERER_QUERY_PARAM_NAME => Mage::helper('core')->urlEncode(
                                Mage::getUrl('*/*/*', array('_current' => true)) .
                                '#review-form')
                    )
                )
            );
        }

        $this->setTemplate('review/form.phtml')
            ->assign('data', $data)
            ->assign('messages', Mage::getSingleton('review/session')->getMessages(true));
    }
}