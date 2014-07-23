<?php

class Gri_Alipay_Model_System_Config_Source_Timeout
{

    public function toOptionArray()
    {
        return array(
            array('value' => 0, 'label' => Mage::helper('adminhtml')->__('No')),
            array('value' => 1, 'label' => Mage::helper('alipay')->__('Dynamic: According to Auto Cancellation')),
            array('value' => 2, 'label' => Mage::helper('alipay')->__('Specified Length')),
        );
    }

    public function toArray()
    {
        return array(
            0 => Mage::helper('adminhtml')->__('No'),
            1 => Mage::helper('alipay')->__('Dynamic: According to Auto Cancellation'),
            2 => Mage::helper('alipay')->__('Specified Length'),
        );
    }
}
