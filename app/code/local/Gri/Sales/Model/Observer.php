<?php

class Gri_Sales_Model_Observer extends Varien_Object
{

    /**
     * @return Gri_Sales_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('gri_sales');
    }

    public function addSalesRuleAction(Varien_Event_Observer $observer)
    {
        /* @var $form Varien_Data_Form */
        $form = $observer->getEvent()->getForm();
        /* @var $field Varien_Data_Form_Element_Select */
        $field = $form->getElement('simple_action');
        $values = $field->getValues();
        $values[] = array(
            'value' => Gri_Sales_Helper_Data::UPGRADE_SHIPPING_METHOD,
            'label' => $this->_getHelper()->__('Free upgrade to express shipping method'),
        );
        $field->setValues($values);
    }

    public function afterRefund(Varien_Event_Observer $observer)
    {
        /* @var $creditmemo Gri_Sales_Model_Order_Creditmemo */
        $creditmemo = $observer->getEvent()->getCreditmemo();
        $creditmemo->setRefundedAt(Varien_Date::now());
        $this->_getHelper()->setDefaultOrderStatusForState('complete', 'partial_refunded');
        $order = $creditmemo->getOrder();
        $order->getTotalRefunded() * 1 && $order->canCreditmemo() and
            $order->addStatusHistoryComment(
                $this->_getHelper()->__('Refunded creditmemo #%s', $creditmemo->getIncrementId()),
                'partial_refunded'
            );
    }

    public function scheduledAutoCancelOrder()
    {
        $life = Mage::getStoreConfig(Gri_Sales_Model_Order::CONFIG_PATH_AUTO_CANCELLATION) * 60;
        $timeLimit = Varien_Date::formatDate(time() - $life);
        $logFile = 'auto.cancel.order.log';
        /* @var $orders Mage_Sales_Model_Resource_Order_Collection */
        $orders = Mage::getResourceModel('sales/order_collection');
        $orders->addFieldToFilter('state', 'pending_payment')
            ->addFieldToFilter('status', 'pending_payment')
            ->addFieldToFilter('updated_at', array('lt' => $timeLimit));
        /* @var $order Gri_Sales_Model_Order */
        foreach ($orders as $order) {
            $order->cancelWithComment(Mage::helper('gri_sales')->__('Auto Cancel Order'))->save();
            Mage::log('Canceled order: ' . $order->getRealOrderId(), NULL, $logFile);
        }
    }
}
