<?php
require_once ('app/code/core/Mage/Checkout/controllers/OnepageController.php');

/**
 * Class Gri_CheckoutCustom_OnepageController
 * @method Gri_CheckoutCustom_Model_Type_Onepage getOnepage()
 */
class Gri_CheckoutCustom_OnepageController extends Mage_Checkout_OnepageController
{

    public function billingAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody($this->getLayout()->getBlock('checkout.onepage.billing')->toHtml());
    }

    public function saveBillingAction()
    {
        $this->getOnepage()->getQuote()->setFapiao($this->getRequest()->getParam('need_fapiao') ?
            $this->getRequest()->getParam('fapiao') : ''
        );
        parent::saveBillingAction();
    }

    public function saveShippingMethodAction()
    {
        $this->getOnepage()->getQuote()->setFapiao($this->getRequest()->getParam('need_fapiao') ?
            $this->getRequest()->getParam('fapiao') : ''
        );
        $this->getOnepage()->getQuote()->setRemarks($this->getRequest()->getParam('remarks'));
        parent::saveShippingMethodAction();
    }

    public function successAction()
    {
        $session = $this->getOnepage()->getCheckout();
        if (!$session->getLastSuccessQuoteId()) {
            $this->_redirect('checkout/cart');
            return;
        }

        $lastQuoteId = $session->getLastQuoteId();
        $lastOrderId = $session->getLastOrderId();
        $lastRecurringProfiles = $session->getLastRecurringProfileIds();
        if (!$lastQuoteId || (!$lastOrderId && empty($lastRecurringProfiles))) {
            $this->_redirect('checkout/cart');
            return;
        }

        $this->loadLayout();
        $this->_initLayoutMessages('checkout/session');
        Mage::dispatchEvent('checkout_onepage_controller_success_action', array('order_ids' => array($lastOrderId)));
        $this->renderLayout();
    }

    public function useAddressAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
        if ($this->getRequest()->isPost()) {
            $customerAddressId = $this->getRequest()->getPost('billing_address_id', FALSE);

            $result = $this->getOnepage()->useAddress($customerAddressId);

            if (!isset($result['error'])) {
                $result['goto_section'] = 'shipping_method';
                $result['update_section'] = array(
                    'name' => 'shipping-method',
                    'html' => $this->_getShippingMethodsHtml()
                );

                $result['allow_sections'] = array('shipping');
                $result['duplicateBillingInfo'] = 'true';
            }

            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
    }
}
