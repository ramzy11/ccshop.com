<?php

class Gri_Sales_Block_Adminhtml_Order_Totals extends Mage_Adminhtml_Block_Sales_Order_Totals
{

    protected function _initTotals()
    {
        parent::_initTotals();
        $this->getTotal('grand_total')->setLabel($this->__('Money Received'));
        return $this;
    }
}
