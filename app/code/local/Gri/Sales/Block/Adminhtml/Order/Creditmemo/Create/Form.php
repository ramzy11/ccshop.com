<?php

class Gri_Sales_Block_Adminhtml_Order_Creditmemo_Create_Form extends
    Mage_Adminhtml_Block_Sales_Order_Creditmemo_Create_Form
{

    public function getSaveUrl()
    {
        if (Mage::registry('cancel_order'))
            return $this->getUrl('gri_sales/order_creditmemo/save', array('_current' => true));
        if (Mage::registry('edit'))
            return $this->getUrl('gri_sales/order_creditmemo/update', array('_current' => true));
        return parent::getSaveUrl();
    }
}
