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
