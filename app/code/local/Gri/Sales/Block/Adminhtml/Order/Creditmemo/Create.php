<?php

/**
 * @method Gri_Sales_Model_Order_Creditmemo getCreditmemo() Get Current Creditmemo
 */
class Gri_Sales_Block_Adminhtml_Order_Creditmemo_Create extends Mage_Adminhtml_Block_Sales_Order_Creditmemo_Create
{

    public function getBackUrl()
    {
        $adminFrontName = (string)Mage::getConfig()->getNode(Mage_Adminhtml_Helper_Data::XML_PATH_ADMINHTML_ROUTER_FRONTNAME);
        Mage::app()->getFrontController()->getRouterByRoute('admin')->addModule($adminFrontName, 'Mage_Adminhtml', 'admin');
        $adminHelper = Mage::helper('adminhtml');
        $creditmemo = $this->getCreditmemo();
        if ($creditmemo->getId() && $creditmemo->canUpdate()) {
            return $adminHelper->getUrl('admin/sales_order_creditmemo/view', array('creditmemo_id'=>$this->getCreditmemo()->getId()));
        }
        return $adminHelper->getUrl('admin/sales_order/view', array('order_id'=>$this->getCreditmemo()->getOrderId()));
    }

    public function getHeaderText()
    {
        if ($this->_getData('header_text')) return $this->_getData('header_text');
        $creditmemo = $this->getCreditmemo();
        if ($creditmemo->getId() && $creditmemo->canUpdate()) {
            if ($this->getCreditmemo()->getEmailSent()) {
                $emailSent = Mage::helper('sales')->__('the credit memo email was sent');
            }
            else {
                $emailSent = Mage::helper('sales')->__('the credit memo email is not sent');
            }
            return Mage::helper('sales')->__('Edit Credit Memo #%1$s | %3$s | %2$s (%4$s)', $this->getCreditmemo()->getIncrementId(), $this->formatDate($this->getCreditmemo()->getCreatedAtDate(), 'medium', true), $this->getCreditmemo()->getStateName(), $emailSent);
        }
        else return parent::getHeaderText();
    }
}
