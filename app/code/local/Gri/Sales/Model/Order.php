<?php

/**
 * @method string getRemarks()
 */
class Gri_Sales_Model_Order extends Mage_Sales_Model_Order
{
    const CONFIG_PATH_AUTO_CANCELLATION = 'sales/auto_cancel_order/life';

    protected function _afterLoad()
    {
        parent::_afterLoad();
        $this->setMoneyTotalRefunded($this->getTotalRefunded() - $this->getCustomerBalanceTotalRefunded());
        $this->setBaseMoneyTotalRefunded($this->getBaseTotalRefunded() - $this->getBaseCustomerBalanceTotalRefunded());
        return $this;
    }

    /**
     * Check order state before saving
     */
    protected function _checkState()
    {
        if (!$this->getId()) {
            return $this;
        }

        $userNotification = $this->hasCustomerNoteNotify() ? $this->getCustomerNoteNotify() : NULL;

        if (!$this->isCanceled()
            && !$this->canUnhold()
            && !$this->canInvoice()
            && !$this->canShip()
        ) {
            if (abs($this->getTotalPaid() - $this->getTotalRefunded()) > .0001 || !($this->getTotalPaid() * 1)) {
                if ($this->getState() !== self::STATE_COMPLETE) {
                    $this->_setState(self::STATE_COMPLETE, TRUE, '', $userNotification);
                }
            } else if (($this->getTotalRefunded() * 1) || $this->hasForcedCanCreditmemo()) {
                if ($this->getState() !== self::STATE_CLOSED) {
                    $this->_setState(self::STATE_CLOSED, TRUE, '', $userNotification);
                }
            }
        }

        if ($this->getState() == self::STATE_NEW && $this->getIsInProcess()) {
            $this->setState(self::STATE_PROCESSING, TRUE, '', $userNotification);
        }
        return $this;
    }

    public function cancelWithComment($comment = '')
    {
        if ($this->canCancel()) {
            $this->getPayment()->cancel();
            $this->registerCancellation($comment);
            Mage::dispatchEvent('order_cancel_after', array('order' => $this));
        }
        return $this;
    }

    public function canReorder()
    {
        return ($this->getState() == 'canceled' || $this->getState() == 'complete' || $this->getState() == 'closed') &&
            parent::canReorder();
    }

    public function getCustomerName()
    {
        if ($elements = Mage::getStoreConfig(Gri_Customer_Model_Customer::CONFIG_PATH_NAME_ELEMENTS)) {
            return strtr($elements, $this->getNameElements());
        }
        else return parent::getCustomerName();
    }

    public function getNameElements(array $attributes = NULL)
    {
        $result = array();
        $attributes === NULL and $attributes = array(
            'prefix', 'firstname', 'middlename', 'lastname', 'suffix'
        );
        foreach ($attributes as $k) {
            $result['{{' . $k . '}}'] = $this->getData('customer_' . $k);
        }
        return $result;
    }

    public function getTransaction($type, $txnId = NULL, $parentTxnId = NULL)
    {
        /* @var Mage_Sales_Model_Order_Payment_Transaction $transaction */
        foreach ($this->getTransactionsCollection() as $transaction) {
            if ($transaction->getTxnType() == $type &&
                (!$txnId || $transaction->getTxnId() == $txnId) &&
                (!$parentTxnId || $transaction->getParentTxnId() == $parentTxnId)
            ) return $transaction;
        }
        return FALSE;
    }

    /**
     * @return Mage_Sales_Model_Resource_Order_Payment_Transaction_Collection
     */
    public function getTransactionsCollection()
    {
        if ($this->_getData('transactions_collection') === NULL) {
            /* @var Mage_Sales_Model_Resource_Order_Payment_Transaction_Collection $collection */
            $collection = Mage::getModel('sales/order_payment_transaction')->getCollection();
            $collection->setOrderFilter($this)
                ->setOrder('created_at', Varien_Data_Collection::SORT_ORDER_DESC)
                ->setOrder('transaction_id', Varien_Data_Collection::SORT_ORDER_DESC);
            $this->setData('transactions_collection', $collection);
        }
        return $this->_getData('transactions_collection');
    }
}
