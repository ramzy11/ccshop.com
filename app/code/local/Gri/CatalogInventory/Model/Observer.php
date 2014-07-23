<?php

class Gri_CatalogInventory_Model_Observer extends Mage_CatalogInventory_Model_Observer
{

    /**
     * Cancel order item
     *
     * @param   Varien_Event_Observer $observer
     * @return  Mage_CatalogInventory_Model_Observer
     */
    public function cancelOrderItem($observer)
    {
        /* @var $item Mage_Sales_Model_Order_Item */
        $item = $observer->getEvent()->getItem();

        // Skip pending orders
        if ($item->getOrder()->getStatus() == 'pending') return $this;
        return parent::cancelOrderItem($observer);
    }

    public function saveInventoryData($observer)
    {
        /* @var $product Gri_CatalogCustom_Model_Product */
        $product = $observer->getEvent()->getProduct();
        if (($item = $product->getStockItem()) &&
            !$item->verifyStock($item->getOrigData('qty')) &&
            $item->verifyStock($product->getStockData('qty'))
        ) {
            $stockData = $product->getStockData();
            $stockData['is_in_stock'] = 1;
            $product->setStockData($stockData);
            $item->setIsInStock(TRUE)
                ->setStockStatusChangedAutomaticallyFlag(TRUE);
        }
        return parent::saveInventoryData($observer);
    }

    public function subtractQuoteInventory(Varien_Event_Observer $observer)
    {
        /* @var $quote Mage_Sales_Model_Quote */
        $quote = $observer->getEvent()->getQuote();

        // Don't subtract inventory if not paid
        if (
            !$quote->hasData('inventory_processed') &&
            !$observer->getEvent()->getHasPaid()
        ) {
            $quote->setInventoryProcessed(TRUE);
        }
        if (!$quote->getInventoryProcessed()) {
            // Save qty_ordered for product filter
            Mage::helper('gri_sales')->updateProductQtyOrdered($quote->getAllItems());
        }
        return parent::subtractQuoteInventory($observer);
    }

    public function subtractQuoteInventoryAfterPaid(Varien_Event_Observer $observer)
    {
        $observer->getEvent()->setHasPaid(TRUE);
        Mage::register('force_inventory_subtract', TRUE);
        /* @var $invoice Mage_Sales_Model_Order_Invoice */
        $invoice = $observer->getEvent()->getInvoice();
        $order = $invoice->getOrder();
        /* @var $quote Mage_Sales_Model_Quote */
        $quote = Mage::getModel('sales/quote');
        $quote->setStore($order->getStore());
        $quote->load($order->getQuoteId());

        $observer->getEvent()->setOrder($order);
        $observer->getEvent()->setQuote($quote);
        return $this->subtractQuoteInventory($observer);
    }
}
