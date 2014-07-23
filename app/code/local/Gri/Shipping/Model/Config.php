<?php

class Gri_Shipping_Model_Config extends Mage_Shipping_Model_Config
{

    public function getAllCarriers($store = NULL)
    {
        $carriers = parent::getAllCarriers($store);
        /* @var $model Mage_Shipping_Model_Carrier_Abstract */
        foreach ($carriers as $code => $model) {
            if (!$model->isActive()) unset($carriers[$code]);
        }
        return $carriers;
    }
}
