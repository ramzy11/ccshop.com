<?php

class Gri_Alipay_Model_Source_Servicetype
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'trade_create_by_buyer', 'label' => Mage::helper('alipay')->__('Standard Double Gateways')),
            //array('value' => 'create_digital_goods_trade_p', 'label' => Mage::helper('alipay')->__('Virtual Products')),
            array('value' => 'create_partner_trade_by_buyer', 'label' => Mage::helper('alipay')->__('Secured Payment')),
            array('value' => 'create_direct_pay_by_user', 'label' => Mage::helper('alipay')->__('Direct Payment')),
        );
    }
}
