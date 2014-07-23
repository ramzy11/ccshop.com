<?php

class Gri_Paypal_Model_Info extends Mage_Paypal_Model_Info
{

    public function getPaymentInfo(Mage_Payment_Model_Info $payment, $labelValuesOnly = FALSE)
    {
        /* @var $payment Mage_Sales_Model_Order_Payment */
        $info = parent::getPaymentInfo($payment, $labelValuesOnly);
        // Only process if has transactions
        if ($payment->getLastTransId() && $order = $payment->getOrder()) {
            /* @var $transactions Mage_Sales_Model_Resource_Order_Payment_Transaction_Collection */
            $transactions = Mage::getResourceModel('sales/order_payment_transaction_collection');
            $transactions->addOrderIdFilter($order->getId())
                ->addTxnTypeFilter('capture')
                ->setPageSize(1);
            $transactions->getSelect()->reset('columns')->columns('main_table.txn_id');

            $keys = array_keys($info);
            $lastElement = array(
                'key' => end($keys),
                'value' => array_pop($info),
            );

            // Insert payment transaction id in front of last transaction id
            $label = Mage::helper('sales')->__('Transaction ID');
            $txnId = $transactions->getFirstItem()->getTxnId();
            if ($labelValuesOnly) {
                $info[$label] = $txnId;
            } else {
                $info['trans_id'] = array('label' => $label, 'value' => $txnId);
            }
            $info[$lastElement['key']] = $lastElement['value'];
        }
        return $info;
    }
}
