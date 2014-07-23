<?php

class Gri_Alipay_Model_Source_Bank
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'ALIPAY', 'label' => Mage::helper('alipay')->__('Alipay')),
            array('value' => 'BOCB2C', 'label' => Mage::helper('alipay')->__('Bank of China')),
            array('value' => 'ICBCB2C', 'label' => Mage::helper('alipay')->__('Industrial and Commercial Bank of China')),
            array('value' => 'CMB', 'label' => Mage::helper('alipay')->__('China Merchants Bank')),
            array('value' => 'CCB', 'label' => Mage::helper('alipay')->__('China Construction Bank')),

            array('value' => 'ABC', 'label' => Mage::helper('alipay')->__('Agricultural Bank of China')),
            array('value' => 'SPDB', 'label' => Mage::helper('alipay')->__('SPD Bank')),
            array('value' => 'CIB', 'label' => Mage::helper('alipay')->__('Xingye Bank')),
            array('value' => 'GDB', 'label' => Mage::helper('alipay')->__('Guangdong Development Bank')),
            array('value' => 'SDB', 'label' => Mage::helper('alipay')->__('Shenzhen Development Bank')),
            array('value' => 'CMBC', 'label' => Mage::helper('alipay')->__('China Mingshen Banking')),

            array('value' => 'COMM', 'label' => Mage::helper('alipay')->__('Bank of Communications')),
            array('value' => 'CITIC', 'label' => Mage::helper('alipay')->__('Zhongxin Bank')),
            array('value' => 'CEBBANK', 'label' => Mage::helper('alipay')->__('Guangda Bank')),
            array('value' => 'NBBANK', 'label' => Mage::helper('alipay')->__('Bank of Ning Bo')),
            array('value' => 'HZCBB2C', 'label' => Mage::helper('alipay')->__('Hangzhou Bank')),
            array('value' => 'SHBANK', 'label' => Mage::helper('alipay')->__('Shanghai Bank')),
            array('value' => 'SPABANK', 'label' => Mage::helper('alipay')->__('Ping An Bank')),

            array('value' => 'BJRCB', 'label' => Mage::helper('alipay')->__('Beijing Rural Commercial Bank')),
            array('value' => 'FDB', 'label' => Mage::helper('alipay')->__('Fudian Bank')),
            array('value' => 'PSBC-DEBIT', 'label' => Mage::helper('alipay')->__('Postal Savings Bank of China')),
            array('value' => 'BJBANK', 'label' => Mage::helper('alipay')->__('Beijing Bank')),

//            array('value' => 'abc1003', 'label' => Mage::helper('alipay')->__('VISA')),
//            array('value' => 'abc1004', 'label' => Mage::helper('alipay')->__('MASTER')),
        );
    }

    public function getLabel($code)
    {
        foreach ($this->toOptionArray() as $bank) {
            if ($code == $bank['value']) {
                return $bank['label'];
            }
        }
        return;
    }
}
