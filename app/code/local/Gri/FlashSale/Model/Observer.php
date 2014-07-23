<?php

class Gri_FlashSale_Model_Observer extends Varien_Object
{

    /**
     * @return Gri_FlashSale_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('gri_flashsale');
    }

    public function cancelOrderItem(Varien_Event_Observer $observer)
    {
        /* @var $item Mage_Sales_Model_Order_Item */
        $item = $observer->getEvent()->getItem();
        $qty = $item->getQtyOrdered() - max($item->getQtyShipped(), $item->getQtyInvoiced()) - $item->getQtyCanceled();
        $item->setTotalQty($qty);
        $this->_getHelper()->updateQtyOrdered(array($item), '-');
    }

    public function checkIsSalable(Varien_Event_Observer $observer)
    {
        /* @var $product Gri_CatalogCustom_Model_Product */
        $product = $observer->getEvent()->getProduct();
        $salable = $observer->getEvent()->getSalable();
        if ($salable->getIsSalable() &&
            Mage::registry('remove_unavailable_products') &&
            ($flashQty = 1 * $product->getFlashSaleParentQty()) &&
            ($flashQtyOrdered = 1 * $product->getFlashSaleParentQtyOrdered()) &&
            ($flashQtyOrdered >= $flashQty)
        ) {
            $salable->setIsSalable(FALSE);
        }
    }

    public function initProductView(Varien_Event_Observer $observer)
    {
        /* @var $product Gri_CatalogCustom_Model_Product */
        $product = $observer->getEvent()->getProduct();
        /* @var $flashSaleProduct Gri_FlashSale_Model_FlashSale_Product */
        if (Mage::registry('remove_unavailable_products') &&
            ($flashSaleProduct = Mage::registry('flash_sale_product'))
        ) {
            $product->setFlashSaleParentQty($flashSaleProduct->getParentQty())
                ->setFlashSaleParentQtyOrdered($flashSaleProduct->getResource()->getParentQtyOrdered($flashSaleProduct));
        }
    }

    public function revertQtyOrdered(Varien_Event_Observer $observer)
    {
        /* @var $quote Mage_Sales_Model_Quote */
        $quote = $observer->getEvent()->getQuote();

        if (!$quote->getFlashSaleProcessed()) {
            // Save qty_ordered for flash sale products
            $this->_getHelper()->updateQtyOrdered($quote->getAllItems(), '-');
            $quote->setFlashSaleProcessed(TRUE);
        }
    }

    public function updateFinalPrice(Varien_Event_Observer $observer)
    {
        /* @var $product Gri_CatalogCustom_Model_Product */
        $product = $observer->getEvent()->getProduct();
        $this->_getHelper()->updateFinalPrice($product);
    }

    public function updateQtyOrdered(Varien_Event_Observer $observer)
    {
        /* @var $quote Mage_Sales_Model_Quote */
        $quote = $observer->getEvent()->getQuote();

        if (!$quote->getFlashSaleProcessed()) {
            // Save qty_ordered for flash sale products
            $this->_getHelper()->updateQtyOrdered($quote->getAllItems());
            $quote->setFlashSaleProcessed(TRUE);
        }
    }

    public function updateFlashSaleCategoryProducts(Varien_Event_Observer $observer)
    {
        /* @var $flashSale Gri_FlashSale_Model_FlashSale */
        $flashSale = $observer->getEvent()->getObject();
        if( $flashSale instanceof Gri_FlashSale_Model_FlashSale ) {
            $this->_getHelper()->updateFlashSaleCategoryProducts();
        }
    }
}
