<?php

class Gri_Hamper_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_NODE_HAMPER_PRODUCT_TYPE = 'global/catalog/product/type/hamper';
    /**
     * @var Gri_Hamper_Block_Quote_Item_Renderer
     */
    protected $_quoteItemRenderer;

    public function __construct()
    {
        ini_set('xdebug.max_nesting_level', 150);
    }

    /**
     * Retrieve array of allowed product types for hamper selection product
     *
     * @return array
     */
    public function getAllowedSelectionTypes()
    {
        $config = Mage::getConfig()->getNode(self::XML_NODE_HAMPER_PRODUCT_TYPE);
        return array_keys($config->allowed_selection_types->asArray());
    }

    /**
     * @param Mage_Sales_Model_Quote_Item|Mage_Sales_Model_Order_Item $item
     * @return mixed
     */
    public function getIsGift($item)
    {
        $quoteItem = $this->getQuoteItem($item);
        return $quoteItem ? $quoteItem->getOptionByCode('is_gift') : FALSE;
    }

    /**
     * @param Varien_Object $buyRequest
     * @return array
     */
    public function getMessage($buyRequest)
    {
        $message = $buyRequest->getHamperMessage();
        $message = array_map('trim', (array)$message);
        $hasMessage = FALSE;
        foreach ($message as $v) {
            if ($v !== '') {
                $hasMessage = TRUE;
                break;
            }
        }
        return $hasMessage ? $message : array();
    }

    /**
     * @param Mage_Sales_Model_Quote_Item|Mage_Sales_Model_Order_Item $item
     * @return Mage_Sales_Model_Quote_Item|Mage_Sales_Model_Order_Item|Mage_Sales_Model_Quote_Item
     */
    public function getQuoteItem($item)
    {
        $quoteItem = $item;
        if ($item instanceof Mage_Sales_Model_Order_Item) {
            if ($item->getQuoteItem() instanceof Mage_Sales_Model_Quote_Item) $quoteItem = $item->getQuoteItem();
            else if ($item->getQuoteItemId()) {
                $order = $item->getOrder();
                /* @var $quote Mage_Sales_Model_Quote */
                if (!$quote = $order->getQuote()) {
                    $quote = Mage::getModel('sales/quote');
                    $quote->setStoreId($order->getStoreId())
                        ->load($order->getQuoteId());
                    $order->setQuote($quote);
                }
                $quoteItem = $quote->getItemById($item->getQuoteItemId());
                $item->setQuoteItem($quoteItem);
            }
        }
        return $quoteItem;
    }

    /**
     * @param Mage_Checkout_Block_Cart_Item_Renderer_Configurable|Mage_Checkout_Block_Cart_Item_Renderer $default
     * @param Mage_Sales_Model_Quote_Item $item
     * @param Mage_Catalog_Model_Product $product
     * @return Gri_Hamper_Block_Quote_Item_Renderer
     */
    public function getQuoteItemRenderer($default, $item = NULL, $product = NULL)
    {
        $this->_quoteItemRenderer === NULL and $this->_quoteItemRenderer = $default->getLayout()
            ->createBlock('hamper/quote_item_renderer', 'hamper.renderer');
        $item or $item = $default->getItem();
        $product or $product = $item->getProduct();
        $this->_quoteItemRenderer->setDefaultRenderer($default)
            ->setItem($item)->setProduct($product);
        return $this->_quoteItemRenderer;
    }
}
