<?php

class Gri_CheckoutCustom_Block_Onepage_Login extends Mage_Checkout_Block_Onepage_Login
{

    public function isAllowedGuestCheckout()
    {
        return Mage::helper('checkout')->isAllowedGuestCheckout($this->getQuote());
    }
}
