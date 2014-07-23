<?php

/**
 * Class Gri_Sales_Model_Order_Creditmemo
 * @method integer getCanCreateOrder()
 * @method float getBaseMoneyTotalRefunded()
 * @method float getMoneyTotalRefunded()
 * @method string getRefundedAt()
 * @method setRealRegister(boolean $realRegister)
 * @method Gri_Sales_Model_Order_Creditmemo setCanCreateOrder(integer $flag)
 * @method Gri_Sales_Model_Order_Creditmemo setRefundedAt(string $time)
 */
class Gri_Sales_Model_Order_Creditmemo extends Mage_Sales_Model_Order_Creditmemo
{
    const STATE_ORDER_CANCELED = 4;
    const STATE_NOTIFIED = 5;
    const STATE_CLOSED = 6;
    const STATE_CANCELED_CLOSED = 7;
    const STATE_UPDATED = 8;
    const STATUS_ORDER_CANCELED = 1;
    const STATUS_NOTIFIED = 2;
    const STATUS_CLOSED = 3;

    protected function _afterLoad()
    {
        parent::_afterLoad();
        $this->setMoneyTotalRefunded($this->getGrandTotal() - $this->getCustomerBalanceTotalRefunded());
        $this->setBaseMoneyTotalRefunded($this->getBaseGrandTotal() - $this->getBaseCustomerBalanceTotalRefunded());
        return $this;
    }

    protected function _construct()
    {
        $this->setState(self::STATE_OPEN);
        parent::_construct();
    }

    /**
     * Adds comment to credit memo with additional possibility to send it to customer via email
     * and show it in customer account
     *
     * @param $comment
     * @param bool $notify
     * @param bool $visibleOnFront
     * @param string $type
     *
     * @return Mage_Sales_Model_Order_Creditmemo
     */
    public function addComment($comment, $notify = FALSE, $visibleOnFront = FALSE, $type = '')
    {
        if (!($comment instanceof Mage_Sales_Model_Order_Creditmemo_Comment)) {
            $comment = Mage::getModel('sales/order_creditmemo_comment')
                ->setComment($comment)
                ->setType($type)
                ->setIsCustomerNotified($notify)
                ->setIsVisibleOnFront($visibleOnFront);
        }
        $comment->setCreditmemo($this)
            ->setParentId($this->getId())
            ->setStoreId($this->getStoreId());
        if (!$comment->getId()) {
            $this->getCommentsCollection()->addItem($comment);
        }
        $this->_hasDataChanges = TRUE;
        return $this;
    }

    public function canCancel()
    {
        if (!$this->getSalesHelper()->isCreditmemoTwoStep()) {
            return parent::canCancel();
        }
        return in_array($this->getState(), array(
            self::STATE_OPEN,
            self::STATE_ORDER_CANCELED,
            self::STATE_NOTIFIED,
            self::STATE_UPDATED,
        ));
    }

    public function cancel()
    {
        $this->setState(self::STATE_CANCELED);
        Mage::dispatchEvent('sales_order_creditmemo_cancel', array($this->_eventObject=>$this));
        return $this;
    }

    public function canRefund()
    {
        if (!$this->getSalesHelper()->isCreditmemoTwoStep()) {
            return parent::canRefund();
        }
        if (!$this->getSalesHelper()->needNotifyBeforeConfirm()) {
            return !in_array($this->getState(), array(
                self::STATE_CANCELED,
                self::STATE_CANCELED_CLOSED,
                self::STATE_REFUNDED,
            )) && $this->getOrder()->canCreditmemo() && $this->canRegister();
        }
        return in_array($this->getState(), array(
            self::STATE_NOTIFIED,
        )) && $this->getOrder()->canCreditmemo() && $this->canRegister();
    }

    public function canRegister()
    {
        $baseOrderRefund = Mage::app()->getStore()->roundPrice(
            $this->getOrder()->getBaseTotalRefunded() + $this->getBaseGrandTotal()
        );
        $baseTotalPaid = Mage::app()->getStore()->roundPrice($this->getOrder()->getBaseTotalPaid());
        return $baseTotalPaid >= $baseOrderRefund;
    }

    public function canUpdate()
    {
        return $this->getSalesHelper()->isCreditmemoTwoStep() && in_array($this->getState(), array(
            self::STATE_NOTIFIED,
            self::STATE_OPEN,
            self::STATE_ORDER_CANCELED,
        ));
    }

    public function collectTotals()
    {
        if (!($this->getOrder()->getBaseShippingAmount() * 1) && $this->getBaseShippingAmount() * 1) {
            Mage::throwException(Mage::helper('gri_sales')->__('Invalid shipping amount'));
        }
        parent::collectTotals();
        $this->setMoneyTotalRefunded($this->getGrandTotal() - $this->getCustomerBalanceTotalRefunded());
        $this->setBaseMoneyTotalRefunded($this->getBaseGrandTotal() - $this->getBaseCustomerBalanceTotalRefunded());
        return $this;
    }

    public function getBaseCustomerBalanceReturnMax()
    {
        $max = $this->getBaseCustomerBalanceAmount() + $this->getBaseGiftCardsAmount() + $this->getBaseGrandTotal();
        if (Mage::registry('edit')) {
            if ($existing = $this->getBaseCustomerBalanceTotalRefunded() * 1) {
                return $existing;
            }
            return $max;
        } else if (Mage::registry('update_creditmemo')) return $max;
        return $this->_getData('base_customer_balance_return_max');
    }

    public function getRealRegister()
    {
        if ($this->getSalesHelper()->isCreditmemoTwoStep()) {
            return $this->_getData('real_register');
        }
        return TRUE;
    }

    public function getRefundedAtDate()
    {
        return Mage::app()->getLocale()->date(
            Varien_Date::toTimestamp($this->getRefundedAt()),
            NULL,
            NULL,
            TRUE
        );
    }

    public function getAllItems()
    {
        $this->getBackupCreditmemoId() and $this->setId($this->getBackupCreditmemoId());
        return parent::getAllItems();
    }

    /**
     * @return Gri_Sales_Helper_Data
     */
    public function getSalesHelper()
    {
        return Mage::helper('gri_sales');
    }

    /**
     * Retrieve Creditmemo state name by state identifier
     *
     * @param   int $stateId
     * @return  string
     */
    public function getStateName($stateId = NULL)
    {
        if (is_null($stateId)) {
            $stateId = $this->getState();
        }

        if (is_null(self::$_states)) {
            self::getStates();
        }
        if (isset(self::$_states[$stateId])) {
            return self::$_states[$stateId];
        }
        return Mage::helper('sales')->__('Unknown State');
    }

    public static function getStates()
    {
        if (is_null(self::$_states)) {
            self::$_states = array(
                self::STATE_OPEN       => Mage::helper('sales')->__('Pending'),
                self::STATE_REFUNDED   => Mage::helper('sales')->__('Refunded'),
                self::STATE_CANCELED   => Mage::helper('sales')->__('Canceled'),
                self::STATE_ORDER_CANCELED   => Mage::helper('gri_sales')->__('Order Canceled'),
                self::STATE_NOTIFIED   => Mage::helper('gri_sales')->__('HKAs400 Notified'),
                self::STATE_CLOSED     => Mage::helper('gri_sales')->__('Refunded and Closed'),
                self::STATE_CANCELED_CLOSED  => Mage::helper('gri_sales')->__('Canceled and Closed'),
                self::STATE_UPDATED    => Mage::helper('gri_sales')->__('Updated'),
            );
        }
        return self::$_states;
    }

    public function register()
    {
        if (!$this->getSalesHelper()->isCreditmemoTwoStep()) {
            return parent::register();
        }
        // Two-step creditmemo register
        if ($this->getState() == self::STATE_NOTIFIED || (
            !$this->getSalesHelper()->needNotifyBeforeConfirm() &&
            $this->getRealRegister()
        )) {
            $this->setBackupCreditmemoId($this->getId());
            // Skip checking existing creditmemo
            $this->setId(NULL);
            parent::register();
            $this->setId($this->getBackupCreditmemoId());
        }
        else if (!$this->canRegister()) {
            $baseAvailableRefund = $this->getOrder()->getBaseTotalPaid() - $this->getOrder()->getBaseTotalRefunded();
            Mage::throwException(
                Mage::helper('sales')->__('Maximum amount available to refund is %s', $this->getOrder()->formatBasePrice($baseAvailableRefund))
            );
        }
    }
}
