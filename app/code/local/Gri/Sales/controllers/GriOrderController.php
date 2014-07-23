<?php

class Gri_Sales_GriOrderController extends Mage_Sales_Controller_Abstract
{

    protected $_orderLoaded;

    /**
     * @return Gri_Sales_Model_Order
     */
    protected function _getCurrentOrder()
    {
        return Mage::registry('current_order');
    }

    protected function _loadValidOrder($orderId = NULL)
    {
        if ($this->_orderLoaded === NULL) {
            if ($this->_orderLoaded = parent::_loadValidOrder($orderId)) {
                $order = $this->_getCurrentOrder();
                if ($order->getStatus() != 'pending_payment') $this->_orderLoaded = FALSE;
            }
        }
        return $this->_orderLoaded;
    }

    public function payNowAction()
    {
        if ($this->_loadValidOrder()) {
            $order = $this->_getCurrentOrder();
            $order->cancelWithComment(Mage::helper('gri_sales')->__('Canceled by Customer Pay Now Action'))
                ->save();
            parent::reorderAction();
        }
    }
}
