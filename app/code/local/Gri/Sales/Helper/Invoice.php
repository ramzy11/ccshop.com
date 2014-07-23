<?php

class Gri_Sales_Helper_Invoice extends Mage_Core_Helper_Abstract
{
    /**
     * Retrieve order invoices collection
     *
     * @return unknown
     */
    public function getInvoiceCollectionByOrder(Mage_Sales_Model_Order $order)
    {
        /* @var $invoiceCollection Mage_Sales_Model_Resource_Order_Invoice_Collection */
        $invoiceCollection = Mage::getResourceModel('sales/order_invoice_collection')
                         ->setOrderFilter($order);

        if ($order->getId()) {
            foreach ( $invoiceCollection as $invoice ) {
                $invoice->setOrder($order);
            }
        }

        return $invoiceCollection ;
    }

}