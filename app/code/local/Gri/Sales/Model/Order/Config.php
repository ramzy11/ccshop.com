<?php

class Gri_Sales_Model_Order_Config extends Mage_Sales_Model_Order_Config
{

    public function getStateDefaultStatus($state)
    {
        if (Mage::registry($key = 'order_default_status_for_' . $state)) return Mage::registry($key);
        return parent::getStateDefaultStatus($state);
    }
}
