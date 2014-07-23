<?php

class Gri_CheckoutCustom_Block_Onepage extends Mage_Checkout_Block_Onepage
{
    public function _prepareLayout()
    {
        /* @var $breadcrumbs Mage_Page_Block_Html_Breadcrumbs */
        $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
        $breadcrumbs and $breadcrumbs->addCrumb('home', array('label' => $this->__('Home'), 'title' =>$this->__('Home'),'link'=> Mage::getBaseUrl()));

        $breadcrumbs->addCrumb('checkout', array('label' => $this->__('Checkout'), 'title' =>$this->__('Checkout')));
        return parent::_prepareLayout();
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setStepLabels(array(
            'login' => $this->__('Checkout Method'),
            'billing' => $this->__('Shipping & Delivery'),
            'shipping_method' => $this->__('Shipping & Delivery'),
            'payment' => $this->__('Payment'),
            'review' => $this->__('Order Review')
        ));
        if ($this->isCustomerLoggedIn() && $this->customerHasAddresses()) {
            if (!$this->getCustomer()->getDefaultBillingAddress()) {
                $address = $this->getCustomer()->getDefaultShippingAddress();
                $address or $address = $this->getCustomer()->getAddressesCollection()->getFirstItem();
                $address->setIsDefaultBilling(TRUE)->save();
            }
            if ($this->getQuote()->getShippingAddress()
                && !$this->getQuote()->getShippingAddress()->getShippingRatesCollection()->count()
            ) {
                $this->getQuote()->getShippingAddress()->setCollectShippingRates(true)->save();
            }
        }


    }

    public function getActiveStep()
    {
        return $this->isCustomerLoggedIn() ?
            ($this->customerHasAddresses() ? 'shipping_method' : 'billing') :
            'login';
    }

    public function getStepCodes()
    {
        if ($this->getData('step_codes')) return $this->getData('step_codes');
        return $this->_getStepCodes();
    }

    public function getSteps()
    {
        if ($this->getData('steps') === NULL) {
            $steps = array();
            $stepCodes = $this->getStepCodes();
            $stepLabels = $this->getStepLabels();

            foreach ($stepCodes as $step) {
                $data = $this->getCheckout()->getStepData($step);
                $step == 'login' && $this->isCustomerLoggedIn() and $data['allow'] = FALSE;
                $step == 'shipping' and $data['is_show'] = FALSE;
                isset($stepLabels[$step]) and $data['label'] = $stepLabels[$step];
                $data['label'] = isset($data['label']) ? Mage::helper('checkout')->__($data['label']) : '';
                $steps[$step] = $data;
            }
            $this->setData('steps', $steps);
        }
        return $this->getData('steps');
    }
}
