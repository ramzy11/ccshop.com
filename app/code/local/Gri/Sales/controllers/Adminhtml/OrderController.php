<?php

class Gri_Sales_Adminhtml_OrderController extends Mage_Adminhtml_Controller_Action
{

    /**
     * Initialize order model instance
     *
     * @return Mage_Sales_Model_Order || false
     */
    protected function _initOrder()
    {
        $id = $this->getRequest()->getParam('order_id');
        $order = Mage::getModel('sales/order')->load($id);

        if (!$order->getId()) {
            $this->_getSession()->addError($this->getSalesHelper()->__('This order no longer exists.'));
            $this->_redirect('sales/order/');
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return false;
        }
        Mage::register('sales_order', $order);
        Mage::register('current_order', $order);
        return $order;
    }

    public function cancelAction()
    {
        Mage::register('cancel_order', TRUE);
        $this->getGriHelper()->isCreditmemoTwoStep() or
            $this->getGriHelper()->setDefaultOrderStatusForState('closed', 'canceled_and_refunded');
        $this->_forward('new', 'sales_order_creditmemo', 'admin');
    }

    /**
     * @return Gri_Sales_Helper_Data
     */
    public function getGriHelper()
    {
        return Mage::helper('gri_sales');
    }

    /**
     * @return Mage_Sales_Helper_Data
     */
    public function getSalesHelper()
    {
        return Mage::helper('sales');
    }
}
