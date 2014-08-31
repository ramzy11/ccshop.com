<?php

class Gri_CheckoutCustom_Model_Type_Onepage extends Mage_Checkout_Model_Type_Onepage
{

    public function checkShippingCountryGroup($countryId)
    {
        if ($disallowedProducts = $this->getDisallowedProducts($countryId)) {
            $countryName = Mage::app()->getLocale()->getCountryTranslation($countryId);
            return array(
                'error' => 1,
                'message' => Mage::helper('checkout')->__('%s are not allowed to be shipped to your shipping country (%s)',
                    implode(', ', $disallowedProducts, $countryName)),
            );
        }
        return TRUE;
    }

    public function getDisallowedProducts($countryId)
    {
        $disallowedProductNames = array();
        $countryGroups = $this->getCountryGroups();
        /* @var $item Gri_SalesRule_Model_Quote_Item */
        foreach ($this->getQuote()->getItemsCollection() as $item) {
            $product = $item->getProduct();
            if ($product->getCountryGroup() &&
                isset($countryGroups[$product->getAttributeText('country_group')]) &&
                !in_array($countryId, $countryGroups[$product->getAttributeText('country_group')])
            ) {
                $disallowedProductNames[] = $product->getName();
            }
        }
        return $disallowedProductNames;
    }

    public function getCountryGroups()
    {
        $data = array();
        $groups = Mage::getModel('gri_countrygroup/countrygroup')->getCollection();
        foreach ($groups as $group) {
            $data[$group->getName()] = explode(',', $group->getValue());
        }
        return $data;
    }

    public function saveShipping($data, $customerAddressId)
    {
        // check country group
        $countryId = $data['country_id'];
        $result = $this->checkShippingCountryGroup($countryId);
        if ($result !== TRUE) return $result;
        return parent::saveShipping($data, $customerAddressId);
    }

    public function saveBilling($data, $customerAddressId)
    {
        // check shipping address use from billing address
        if ($data['use_for_shipping']) {
            $countryId = $data['country_id'];
            $result = $this->checkShippingCountryGroup($countryId);
            if ($result !== TRUE) return $result;
        }
        $this->getQuote()->setTotalsCollectedFlag(TRUE);
        $result = parent::saveBilling($data, $customerAddressId);

        $this->getQuote()->setTotalsCollectedFlag(FALSE);
        if (!isset($result['error'])) {
            if (!$this->getQuote()->getCustomerId() && self::METHOD_REGISTER == $this->getQuote()->getCheckoutMethod()) {

                $mobile=$this->getQuote()->getData('customer_mobile');
                $mobileMessage=null;
                if(!empty($mobile)) {
                    /** @var Mage_Customer_Model_Customer $customerModel **/
                    $customerModel = Mage::getModel('customer/customer');
                    /** @var Varien_Data_Collection_Db $exitingCollection **/
                    $exitingCollection = $customerModel->getCollection();
                    $existingCollection = $exitingCollection->addFieldToFilter('mobile', $mobile);
                    if($existingCollection->getSize()>0){
                        $mobileMessage =  Mage::helper('customer')->__('There is already an account associated with this mobile number.Please try another.');
                    }else {
                    }
                }else{
                    $mobileMessage =  Mage::helper('customer')->__('Please fill all required field.');
                }
                if($mobileMessage){
                    return array('error' => 1, 'message' => $mobileMessage);
                }
            }
            /* @var $customerAddress Mage_Customer_Model_Address */
            $customerAddress = Mage::getModel('customer/address');
            if (($addressId = $this->getQuote()->getBillingAddress()->getCustomerAddressId()) &&
                isset($data['edit_mode']) && $data['edit_mode']
            ) {
                /* @var $addressForm Mage_Customer_Model_Form */
                $addressForm = Mage::getModel('customer/form');
                $addressForm->setFormCode('customer_address_edit')
                    ->setEntityType('customer_address')
                    ->setIsAjaxRequest(Mage::app()->getRequest()->isAjax())
                    ->setEntity($customerAddress);
                $addressData = $addressForm->extractData($addressForm->prepareRequest($data));
                $addressForm->compactData($addressData);
                //unset billing address attributes which were not shown in form
                foreach ($addressForm->getAttributes() as $attribute) {
                    if (!isset($data[$attribute->getAttributeCode()])) {
                        $customerAddress->setData($attribute->getAttributeCode(), NULL);
                    } else $customerAddress->setData($attribute->getAttributeCode(), $data[$attribute->getAttributeCode()]);
                }
                $customerAddress->setCustomerId($this->getCustomerSession()->getCustomerId())->setId($addressId)->save();
                $this->getQuote()->getBillingAddress()->importCustomerAddress($customerAddress)
                    ->setSaveInAddressBook(0)
                    ->implodeStreetAddress()
                    ->save();

                $billing = clone $this->getQuote()->getBillingAddress();
                $billing->unsAddressId()->unsAddressType();
                $shipping = $this->getQuote()->getShippingAddress();
                $shippingMethod = $shipping->getShippingMethod();

                // Billing address properties that must be always copied to shipping address
                $requiredBillingAttributes = array('customer_address_id');

                // don't reset original shipping data, if it was not changed by customer
                foreach ($shipping->getData() as $shippingKey => $shippingValue) {
                    if (!is_null($shippingValue) && !is_null($billing->getData($shippingKey))
                        && !isset($data[$shippingKey]) && !in_array($shippingKey, $requiredBillingAttributes)
                    ) {
                        $billing->unsetData($shippingKey);
                    }
                }
                $shipping->addData($billing->getData())
                    ->setSameAsBilling(1)
                    ->setSaveInAddressBook(0)
                    ->setShippingMethod($shippingMethod)
                    ->setCollectShippingRates(TRUE)
                    ->save();
            } else if (!$this->getQuote()->getBillingAddress()->getCustomerAddressId() &&
                $this->getCustomerSession()->getCustomerId()
            ) {
                $customerAddress->setData($this->getQuote()->getBillingAddress()->getData());
                $customerAddress->setIsDefaultBilling(TRUE)->setIsDefaultShipping(TRUE);
                $customerAddress->setCustomerId($this->getCustomerSession()->getCustomerId())->save();
                $this->getQuote()->getBillingAddress()->setCustomerAddressId($customerAddress->getId())->save();
                $this->getQuote()->getShippingAddress()->setCustomerAddressId($customerAddress->getId())->save();
            }
        }
        $this->getQuote()->collectTotals();
        $this->getQuote()->save();
        return $result;
    }

    public function useAddress($customerAddressId)
    {
        /* @var $customerAddress Mage_Customer_Model_Address */
        $customerAddress = Mage::getModel('customer/address')->load($customerAddressId);
        if ($customerAddress->getId()) {
            if ($customerAddress->getCustomerId() != $this->getQuote()->getCustomerId()) {
                return array('error' => 1,
                    'message' => Mage::helper('checkout')->__('Customer Address is not valid.')
                );
            }
            $customerAddress->setIsDefaultBilling(TRUE)->setIsDefaultShipping(TRUE)->save();
            $this->getQuote()->getBillingAddress()->importCustomerAddress($customerAddress)
                ->setSaveInAddressBook(0)
                ->implodeStreetAddress()
                ->save();
            $billing = clone $this->getQuote()->getBillingAddress();
            $billing->unsAddressId()->unsAddressType();
            $shipping = $this->getQuote()->getShippingAddress();
            $shippingMethod = $shipping->getShippingMethod();
            $shipping->addData($billing->getData())
                ->setSameAsBilling(1)
                ->setSaveInAddressBook(0)
                ->setShippingMethod($shippingMethod)
                ->setCollectShippingRates(TRUE)
                ->save();
            $this->getQuote()->collectTotals();
            $this->getQuote()->save();
        }
        return array();
    }
}
