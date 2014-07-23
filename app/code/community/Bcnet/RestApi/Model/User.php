<?php

class Bcnet_RestApi_Model_User extends Bcnet_RestApi_Model_Abstract {
    protected $_attributes;

    public function Register($userData)
    {
        $session = $this->_getSession();
        $result = $this->_getApiResult();
        $session->setEscapeMessages(true);
        $errors = array();
        $customer = Mage::registry('current_customer');

        is_null($customer) && $customer = Mage::getModel('customer/customer');
        isset($userData['isSubscribed']) && !empty($userData['isSubscribed']) && $customer->setIsSubscribed(1);
        $customer->getGroupId();

        try {
            $desPassword = $userData['pwd'];
            $customer->setPassword($desPassword);
            $customer->setData('email', $userData['uname']);
            $customer->setData('firstname', $userData['firstname']);
            $customer->setData('lastname', $userData['lastname']);

            //Todo More Valid
            $validationResult = count($errors) == 0;

            if (true === $validationResult) {
                $customer->save();
                $session->setCustomerAsLoggedIn($customer);
                $customer->sendNewAccountEmail('confirmation');
                $result->setResult('0x0000','Account confirmation is required. Please, check your email');
            } else {
                if (is_array($errors)) {
                    $result->setResult('0x1000', null, null, implode("\n", $errors));
                } else {
                    $result->setResult('0x1010');
                }
            }
        } catch (Mage_Core_Exception $e) {
            if ($e->getCode() === Mage_Customer_Model_Customer::EXCEPTION_EMAIL_EXISTS) {
                $result->setResult('0x1000', null, null, 'An account with this email address already exists.');
                $session->setEscapeMessages(false);
            } else {
                $result->setResult('0x1000', null, null, $e->getMessage());
            }
            return $result->returnResult();
        } catch (Exception $e) {
            $result->setResult('0x1000', null, null, $e->getMessage());
            return $result->returnResult();
        }
        return $result->returnResult();
    }

    public function Update($userData)
    {
        $session = $this->_getSession();
        $result = $this->_getApiResult();
        if( !isset($userData['session']) || !$this->_validateSession($userData['session']) ) {
            $result->setResult('0x0002');
            return $result->returnResult();
        }

        if (!$session->isLoggedIn()) {
            $result->setResult('0x1008');
            return $result->returnResult();
        }

        $customer = $this->_getSession()->getCustomer();
        $customerForm = Mage::getModel('customer/form');
        $customerForm->setFormCode('customer_account_edit')
                ->setEntity($customer);
        $errors = array();
        $customerErrors = $customerForm->validateData($userData);
        if ($customerErrors !== true) {
            $errors = array_merge($customerErrors, $errors);
        } else {
            $customerForm->compactData($userData);
            $customerErrors = $customer->validate();
            if (is_array($customerErrors)) {
                $errors = array_merge($customerErrors, $errors);
            }
        }
        if (!empty($errors)) {
            $result->setResult('0x1000', null, null, implode("\n", $errors));
            return $result->returnResult();
        }
        try {
            $customer->save();
            $this->_getSession()->setCustomer($customer);
            $result->setResult('0x0000');
            return $result->returnResult();
        } catch (Mage_Core_Exception $e) {
            $this->_message($e->getMessage(), self::MESSAGE_STATUS_ERROR);
        } catch (Exception $e) {
            $result->setResult('0x1000', null, null, $e->getMessage());
            return $result->returnResult();
        }
    }

    public function Login($userLoginData)
    {
        $uname = $userLoginData['uname'];
        $pwd = $userLoginData['pwd'];
        /* @var Mage_Customer_Model_Session $session */
        $session = $this->_getSession();

        $desPassword = $pwd;
        $result = $this->_getApiResult();

       if ($uname != null && $pwd != null) {
            try {
                if ($session->login($uname, $desPassword)) {
                    if ($session->getCustomer()->getIsJustConfirmed()) {
                        $session->getCustomer()->sendNewAccountEmail('confirmed');
                    }
                    $result->setResult('0x0000', array('sessionkey'=> md5($session->getEncryptedSessionId())));
                } else {
                    $result->setResult('0x1001');
                }
            } catch (Mage_Core_Exception $e) {
                switch ($e->getCode()) {
                    case Mage_Customer_Model_Customer::EXCEPTION_EMAIL_NOT_CONFIRMED:
                        $result->setResult('0x1000', null, null, $e->getMessage());
                        return $result->returnResult();
                        break;
                    case Mage_Customer_Model_Customer::EXCEPTION_INVALID_EMAIL_OR_PASSWORD:
                        $result->setResult('0x1000', null, null, $e->getMessage());
                        return $result->returnResult();
                        break;
                    default:
                        $result->setResult('0x1000', null, null, $e->getMessage());
                        return $result->returnResult();
                }
            } catch (Exception $e) {
                $result->setResult('0x1000', null, null, $e->getMessage());
            }
        } else {
            $result->setResult('0x1002');
        }
        return $result->returnResult();
    }

    public function FacebookLogin($userData)
    {
        $result = $this->_getApiResult();
        $customer = Mage::getSingleton('customer/customer');
        $session = $this->_getSession();

        if($userData['facebook_uid'] && $userData['first_name'] && $userData['last_name']){
            $collection = $customer->getCollection()
                                    ->addAttributeToFilter('facebook_uid', $userData['facebook_uid'])
                                    ->setPageSize(1);

            $uidExist = (bool)$collection->count();
            if($uidExist){
                $existingCustomer = $collection->getFirstItem();
                $existingCustomer->setFacebookUid('');
                $existingCustomer->getResource()->saveAttribute($existingCustomer->load(NULL), 'facebook_uid');
                $session->setCustomerAsLoggedIn($existingCustomer);
                $result->setResult('0x0000', array('sessionkey' => md5($session->getEncryptedSessionId()), 'facebook_login' => 'login with existing customer'));
            }
            else {
                $randomPassword = $customer->generatePassword(8);
                if($userData['email']){
                    $email = $userData['email'];
                }
                else {
                    $email = $userData['facebook_uid'] . '_from_facebook@123.com';
                }

                $customer->setId(null)
                         ->setSkipConfirmationIfEmail($email)
                         ->setFirstname($userData['first_name'])
                         ->setLastname($userData['last_name'])
                         ->setEmail($email)
                         ->setPassword($randomPassword)
                         ->setConfirmation($randomPassword)
                         ->setFacebookUid($userData['facebook_uid']);
                $errors = array();
                $validationCustomer = $customer->validate();
                if (is_array($validationCustomer)) {
                    $errors = array_merge($validationCustomer, $errors);
                }
                $validationResult = count($errors) == 0;
                if (true === $validationResult) {
                    $customer->save();
                    $session->setCustomerAsLoggedIn($customer);
                    $result->setResult('0x0000', array('sessionkey' => md5($session->getEncryptedSessionId()),'facebook_login' => 'register new customer'));
                }
                else {
                    $result->setResult('0x1000', null, null, 'Failed');
                    return $result->returnResult();
                }
            }

            return $result->returnResult();
        }
        else {
            $result->setResult('0x1000', null, null, 'Parameters (facebook_uid, first_name, last_name) are required.');
            return $result->returnResult();
        }
    }

    public function Get($userData)
    {
        /* @var Mage_Customer_Model_Session $session */
        $session = $this->_getSession();
        $result = $this->_getApiResult();

        if(!isset($userData['session']) || !$this->_validateSession($userData['session'])){
            $result->setResult('0x0002');
            return $result->returnResult();
        }

        if (!$session->isLoggedIn() ) {
            $result->setResult('0x1008');
            return $result->returnResult();
        }

        $fields = $userData['fields'];
        //$session = $this->_getSession();

        $result->setResult('0x0000', $this->_toUserData($session->getCustomer()), $fields);

        return $result->returnResult();
    }

    public function IsExists($userData)
    {
        $info = array (
            'uname_is_exist' => true
        );
        $result = $this->_getApiResult();
        try {
            $customer = Mage::getModel('customer/customer');
            $customer->setStoreId(1);
            $customer->setWebsiteId(1);
            $customer->loadByEmail($userData['uname']);
            if ($customer->getData('entity_id')) {
                $info['uname_is_exist'] = true;
            }
            else {
                $info['uname_is_exist'] = false;
            }
            $result->setResult('0x0000', $info);
        } catch (Exception $e) {
            $result->setResult('0x1000', null, null, $e->getMessage());
            return $result->returnResult();
        }

        return $result->returnResult();
    }

    public function Logout()
    {
        $result = $this->_getApiResult();

        try {
            $this->_getSession()->logout();
            $result->setResult('0x0000');
        } catch (Mage_Core_Exception $e) {
            $result->setResult('0x1000', null, null, $e->getMessage());
            return $result->returnResult();
        } catch (Exception $e) {
            $result->setResult('0x1000', null, null, $e->getMessage());
            return $result->returnResult();
        }

        return $result->returnResult();
    }

    public function Subscribe($userData)
    {
        $result = $this->_getApiResult();
       $email = $userData['uname'];

       if (!Zend_Validate::is($email, 'EmailAddress')) {
            $result->setResult('0x1000', null, null, 'Please enter a valid email address.');
       }
       else {
           try{
                $status = Mage::getModel('newsletter/subscriber')->subscribe($email);
                if ($status == Mage_Newsletter_Model_Subscriber::STATUS_NOT_ACTIVE) {
                    $result->setResult('0x0000', array('msg'=>'Confirmation request has been sent.'));
                }
                else {
                    $result->setResult('0x0000', array('msg'=>'Thank you for your subscription.'));
                }
           }
           catch (Exception $e) {
               $result->setResult('0x1012', array('msg'=>$e->getMessage()));
           }
       }

       return $result->returnResult();
    }

    public function ForgotPwd($userData){
        $result = Mage::getModel('restapi/Result');
        $email = (string) $userData['uname'];
        if ($email && Zend_Validate::is($email, 'EmailAddress')) {
            /** @var $customer Mage_Customer_Model_Customer */
            $customer = Mage::getModel('customer/customer')
                ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                ->loadByEmail($email);

            if ($customer->getId()) {
                try {
                    $newResetPasswordLinkToken = Mage::helper('customer')->generateResetPasswordLinkToken();
                    $customer->changeResetPasswordLinkToken($newResetPasswordLinkToken);
                    $customer->sendPasswordResetConfirmationEmail();
                    $result->setResult('0x0000', array('msg'=>'Please check your email to reset password.'));
                } catch (Exception $exception) {
                    $result->setResult('0x1000', null, null, $exception->getMessage());
                }
            }else{
                $result->setResult('0x1000', null, null, 'No user was found.');
            }
        } else {
            $result->setResult('0x1000', null, null, 'Please enter a valid email address.');
        }
        return $result->returnResult();
    }

    public function AddressGet($queryData)
    {
        $session = $this->_getSession();
        $result = $this->_getApiResult();

        if(!isset($queryData['session']) || !$this->_validateSession($queryData['session'])){
            $result->setResult('0x0002');
            return $result->returnResult();
        }

        if (!$session->isLoggedIn()) {
            $result->setResult('0x1008');
            return $result->returnResult();
        }

        if (isset($queryData['address_book_id'])) {
            $fields = $queryData['fields'];
            $uname = $queryData['uname'];
            $addressId = $queryData['address_book_id'];
            if (!$this->_getSession()->isLoggedIn()) {
                $result->setResult('0x1008');
                return $result->returnResult();
            }
            $customer = $this->_getSession()->getCustomer();
            $addressesData = $customer->getAddressById($addressId);
            $defaultBilling = $customer->getDefaultBillingAddress();
            $defaultShipping = $customer->getDefaultShippingAddress();
            $defaultBillingID = $defaultBilling->getId();
            $defaultShippingID = $defaultShipping->getId();

            if (!is_null($addressesData->getId())) {
                $result->setResult('0x0000', array('address' => $this->_toAddressData($this->prepareAddressData($addressesData, $defaultBillingID, $defaultShippingID), $fields)));
            } else {
                $result->setResult('0x1000', null, null, 'No matched address data.');
            }
            return $result->returnResult();
        }

        $result->setResult('0x1000', null, null, 'Parameter address_book_id missing.');

        return $result->returnResult();
    }

    public function AddressesGet($queryData)
    {
        $session = $this->_getSession();
        $result = $this->_getApiResult();

        if(!isset($queryData['session']) || !$this->_validateSession($queryData['session'])){
            $result->setResult('0x0002');
            return $result->returnResult();
        }

        if (!$session->isLoggedIn()) {
            $result->setResult('0x1008');
            return $result->returnResult();
        }
        $fields = $queryData['fields'];
        $uname = $queryData['uname'];
        $customer = $this->_getSession()->getCustomer();
        $addressesData = $customer->getAddresses();
        $defaultBilling = $customer->getDefaultBillingAddress();
        $defaultShipping = $customer->getDefaultShippingAddress();
        if ($defaultBilling)
            $defaultBillingID = $defaultBilling->getId();
        if ($defaultShipping)
            $defaultShippingID = $defaultShipping->getId();
        if (count($addressesData)) {
            $addresses = array();
            foreach ($addressesData as $value) {
                array_push($addresses, $this->_toAddressData($this->prepareAddressData($value, $defaultBillingID, $defaultShippingID)));
            }
            $result->setResult('0x0000', array('addresses' => $addresses));
        } else {
            $result->setResult('0x0000');
        }
        return $result->returnResult();
    }

    public function AddressSave($addressData)
    {
        $session = $this->_getSession();
        $result = $this->_getApiResult();

        if(!isset($addressData['session']) || !$this->_validateSession($addressData['session'])){
            $result->setResult('0x0002');
            return $result->returnResult();
        }

        if (!$session->isLoggedIn()) {
            $result->setResult('0x1008');
            return $result->returnResult();
        }

        if (!is_null($addressData)) {
            $customer = $session->getCustomer();
            $address = Mage::getModel('customer/address');
            $addressId = isset($addressData['address_book_id']) ? $addressData['address_book_id'] : 0;
            if ($addressId) {
                $existsAddress = $customer->getAddressById($addressId);
                if ($existsAddress->getId() && $existsAddress->getCustomerId() == $customer->getId()) {
                    $address->setId($existsAddress->getId());
                }
            }
            $errors = array();
            try {
                $addressType = explode(',', $addressData['address_type']);
                $address->setCustomerId($customer->getId())
                        ->setIsDefaultBilling(strtolower($addressType[0]) == 'billing' || strtolower($addressType[1]) == 'billing')
                        ->setIsDefaultShipping(strtolower($addressType[0]) == 'shipping' || strtolower($addressType[1]) == 'shipping');
                $address->setLastname($addressData['lastname']);
                $address->setFirstname($addressData['firstname']);
                $address->setSuffix($addressData['suffix']);
                $address->setTelephone($addressData['telephone']);
                $address->setCompany($addressData['company']);
                $address->setFax($addressData['fax']);
                $address->setPostcode($addressData['postcode']);
                $address->setCity($addressData['city']);
                $address->setStreet(array($addressData['address1'], $addressData['address2']));
                $address->setCountry($addressData['country_name']);
                $address->setCountryId($addressData['country_id']);
                if (isset($addressData['state'])) {
                    $address->setRegion($addressData['state']);
                    $address->setRegionId($addressData['state_id']);
                } else {
                    $address->setRegion($addressData['zone_name']);
                    $address->setRegionId($addressData['zone_id']);
                }
                $addressErrors = $address->validate();
                if ($addressErrors !== true) {
                    $errors = array_merge($errors, $addressErrors);
                }
                $addressValidation = count($errors) == 0;
                if (true === $addressValidation) {
                    $address->save();
                    $result->setResult('0x0000', new ArrayObject());
                    return $result->returnResult();
                } else {
                    if (is_array($errors)) {
                        $result->setResult('0x1000', null, null, $errors);
                    } else {
                        $result->setResult('0x1000', null, null, 'Can\'t save address');
                    }
                    return $result->returnResult();
                }
            } catch (Mage_Core_Exception $e) {
                $result->setResult('0x1000', null, null, $e->getMessage());
                return $result->returnResult();
            } catch (Exception $e) {
                $result->setResult('0x1000', null, null, $e->getMessage());
                return $result->returnResult();
            }
        } else {
            $result->setResult('ERROR_0x1011');
            return $result->returnResult();
        }
    }

    public function AddressRemove($addressData)
    {
        $session = $this->_getSession();
        $result = $this->_getApiResult();

        if(!isset($addressData['session']) || !$this->_validateSession($addressData['session'])){
            $result->setResult('0x0002');
            return $result->returnResult();
        }

        if (!$session->isLoggedIn()) {
            $result->setResult('0x1008');
            return $result->returnResult();
        }
        $addressId = $addressData['address_book_id'];
        if ($addressId) {
            $address = Mage::getModel('customer/address')->load($addressId);
            if ($address->getCustomerId() != $this->_getSession()->getCustomerId()) {
                $result->setResult('0x1000', null, null, 'Address does not belong to this customer.');
                return $result->returnResult();
            }
            try {
                $address->delete();
                $result->setResult('0x0000', new ArrayObject());
                return $result->returnResult();
            } catch (Exception $e) {
                $result->setResult('0x1000', null, null, $e->getMessage());
                return $result->returnResult();
            }
        }
    }

    /**
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        $session = Mage::getSingleton('customer/session');
        return $session;
    }

    protected  function _validateSession($sessionId)
    {
        $session = $this->_getSession();
        return md5($session->getEncryptedSessionId()) == $sessionId;
    }

    protected function _toUserData($customerData)
    {
        $userData = array(
            'uname' => $customerData->email,
            'nick' => $customerData->firstname . $customerData->lastname,
            'email' => $customerData->email,
            'fax' => null,
            'telephone' => null,
            'default_address_id' => null,
            'dob' => null,
            'lastname' => $customerData->lastname,
            'firstname' => $customerData->firstname,
            'gender' => null,
            'mobile' => null
        );

        return $userData;
    }

    public function _toAddressData($data)
    {
        $addressData = array (
            'address_book_id' => isset($data['entity_id']) ? $data['entity_id'] : '',
            'firstname' => isset($data['firstname']) ? $data['firstname'] : '',
            'lastname' => isset($data['lastname']) ? $data['lastname'] : '',
            'gender' => isset($data['gender']) ? $data['gender'] : '',
            'suffix' => isset($data['suffix']) ? $data['suffix'] : '',
            'mobile' => isset($data['telephone']) ? $data['telephone'] : '',
            'company' => isset($data['company']) ? $data['company']: '',
            'fax' => isset($data['fax']) ? $data['fax'] : '',
            'telephone' => isset($data['telephone']) ? $data['telephone'] : '',
            'tax_code' => isset($data['tax_code']) ? $data['tax_code'] : '',
            'postcode' => isset($data['postcode']) ? $data['postcode'] : '',
            'city' => isset($data['city']) ? $data['city'] : '',
            'address1' => isset($data['street1']) ? $data['street1'] : '',
            'address2' => isset($data['street2']) ? $data['street2'] : '',
            'country_id' => isset($data['country_id']) ? $data['country_id'] : '',
            'country_name' => isset($data['country']) ? $data['country'] : ''
        );

        if ($data['region_id']) {
            $addressData['zone_id'] = $data['region_id'];
            $addressData['zone_name'] = $data['region'];
        }
        else {
            $addressData['state'] = $data['region'];
        }

        if ($data['is_default_billing'] && $data['is_default_shipping'])
            $addressData['address_type'] = 'billing,shipping';
        else if ($data['is_default_billing'])
            $addressData['address_type'] = 'billing';
        else if ($data['is_default_shipping'])
            $addressData['address_type'] = 'shipping';

        return $addressData;
    }

    public function prepareAddressData(Mage_Customer_Model_Address $address, $defaultBillingID = 0, $defaultShippingID = 0)
    {
        if (!$address) {
            return array();
        }

        $attributes = $this->getAttributes();
        $data = array(
            'entity_id' => $address->getId()
        );
        $data['is_default_billing'] = $defaultBillingID == $data['entity_id'];
        $data['is_default_shipping'] = $defaultShippingID == $data['entity_id'];
        foreach ($attributes as $attribute) {
            if ($attribute->getAttributeCode() == 'country_id') {
                $data['country'] = $address->getCountryModel()->getName();
                $data['country_id'] = $address->getCountryId();
            } else if ($attribute->getAttributeCode() == 'region') {
                $data['region'] = $address->getRegion();
            } else {
                $dataModel = Mage_Customer_Model_Attribute_Data::factory($attribute, $address);
                $value = $dataModel->outputValue(Mage_Customer_Model_Attribute_Data::OUTPUT_FORMAT_ONELINE);
                if ($attribute->getFrontendInput() == 'multiline') {
                    $values = $dataModel->outputValue(Mage_Customer_Model_Attribute_Data::OUTPUT_FORMAT_ARRAY);
                    foreach ($values as $k => $v) {
                        $key = sprintf('%s%d', $attribute->getAttributeCode(), $k + 1);
                        $data[$key] = $v;
                    }
                }
                $data[$attribute->getAttributeCode()] = $value;
            }
        }

        return $data;
    }

    public function getAttributes()
    {
        if (is_null($this->_attributes)) {
            $this->_attributes = array();
            $config = Mage::getSingleton('eav/config');
            foreach ($config->getEntityAttributeCodes('customer_address') as $attributeCode) {
                $this->_attributes[$attributeCode] = $config->getAttribute('customer_address', $attributeCode);
            }
        }

        return $this->_attributes;
    }

    /**
     * @return Bcnet_RestApi_Model_Result
     */
    protected function _getApiResult()
    {
        /* @var Bcnet_RestApi_Model_Result $result */
        $result = Mage::getModel('restapi/Result');
        return $result;
    }

}
