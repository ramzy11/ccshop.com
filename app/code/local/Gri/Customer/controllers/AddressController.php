<?php
/**
 * Customer address controller
 *
 * @category   Gri
 * @package    Gri_Customer
 * @author     BCN Core Team <core@magentocommerce.com>
 */
require_once ('app/code/core/Mage/Customer/controllers/AddressController.php');
class Gri_Customer_AddressController extends Mage_Customer_AddressController
{
    public function formPostAction()
    {
        if (!$this->_validateFormKey()) {
            return $this->_redirect('*/*/');
        }
        // Save data
        if ($this->getRequest()->isPost()) {
            $customer = $this->_getSession()->getCustomer();
            /* @var $address Mage_Customer_Model_Address */
            $address  = Mage::getModel('customer/address');
            $addressId = $this->getRequest()->getParam('id');
            if ($addressId) {
                $existsAddress = $customer->getAddressById($addressId);
                if ($existsAddress->getId() && $existsAddress->getCustomerId() == $customer->getId()) {
                    $address->setId($existsAddress->getId());
                }
            }

            $errors = array();

            /* @var $addressForm Mage_Customer_Model_Form */
            $addressForm = Mage::getModel('customer/form');
            $addressForm->setFormCode('customer_address_edit')
                ->setEntity($address);
            $addressData    = $addressForm->extractData($this->getRequest());
            $addressErrors  = $addressForm->validateData($addressData);
            if ($addressErrors !== true) {
                $errors = $addressErrors;
            }

            try {
                $addressForm->compactData($addressData);
                $address->setCustomerId($customer->getId())
                    ->setIsDefaultBilling($this->getRequest()->getParam('default_billing', false))
                    ->setIsDefaultShipping($this->getRequest()->getParam('default_shipping', false));

                $addressErrors = $address->validate();
                if ($addressErrors !== true) {
                    $errors = array_merge($errors, $addressErrors);
                }

                if (count($errors) === 0) {
                    $address->setData('area_code', $this->getRequest()->getParam('area_code'));
                    $address->save();
                    $this->_getSession()->addSuccess($this->__('The address has been saved.'));
                    $this->_redirectSuccess(Mage::getUrl('*/*/index', array('_secure'=>true)));
                    return;
                } else {
                    $this->_getSession()->setAddressFormData($this->getRequest()->getPost());
                    foreach ($errors as $errorMessage) {
                        $this->_getSession()->addError($errorMessage);
                    }
                }
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->setAddressFormData($this->getRequest()->getPost())
                    ->addException($e, $e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->setAddressFormData($this->getRequest()->getPost())
                    ->addException($e, $this->__('Cannot save address.'));
            }
        }

        return $this->_redirectError(Mage::getUrl('*/*/edit', array('id' => $address->getId())));
    }

    public function changeDefaultAddressAction()
    {
        /* @var $customer Gri_Customer_Model_Customer */
        $customer = $this->_getSession()->getCustomer();

        // Save data
        if ($this->getRequest()->isGet() && $customer->getId()) {

            /* @var $address Mage_Customer_Model_Address */
            $address  = Mage::getModel('customer/address');
            $addressId = $this->getRequest()->getParam('id');
            if ($addressId) {
                $existsAddress = $customer->getAddressById($addressId);
                if ($existsAddress->getId() && $existsAddress->getCustomerId() == $customer->getId()) {
                    $address->load($existsAddress->getId());

                    try {
                        $address->setCustomerId($customer->getId())
                            ->setIsDefaultBilling(1)
                            ->setIsDefaultShipping(1);

                        $address->save();
                        $this->_getSession()->addSuccess($this->__('The address has been saved.'));
                        $this->_redirectSuccess(Mage::getUrl('*/*/index', array('_secure'=>true)));
                        return;
                    } catch (Mage_Core_Exception $e) {
                        $this->_getSession()->setAddressFormData($this->getRequest()->getPost())
                            ->addException($e, $e->getMessage());
                    } catch (Exception $e) {
                        $this->_getSession()->setAddressFormData($this->getRequest()->getPost())
                            ->addException($e, $this->__('Cannot save address.'));
                    }
                }
            }
        }
         else{
             $this->_getSession()->addWarning($this->__('Invalid Data'));
        }

        return $this->_redirectError(Mage::getUrl('*/*/index', array('_secure' => true)));

    }
}
