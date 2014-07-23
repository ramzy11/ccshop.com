<?php

/**
 * Class Gri_Hamper_Block_Quote_Item_Renderer
 * @method Mage_Sales_Model_Quote_Item|Mage_Sales_Model_Order_Item getItem()
 * @method Mage_Catalog_Model_Product getProduct()
 */
class Gri_Hamper_Block_Quote_Item_Renderer extends Mage_Core_Block_Template
{

    /**
     * @return Gri_Hamper_Helper_Data
     */
    protected function _getHelper()
    {
        return $this->helper('hamper');
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     */
    protected function _prepareColorSize($product)
    {
        if ($product->getColor() === NULL) {
            $color = $product->getColorLabel();
            $size = $product->getAttributeText('size_shoes');
            $size or $size = $product->getAttributeText('size_clothing');
            $product->setColor($color)->setSize($size);
        }
    }

    public function getColor($product)
    {
        $this->_prepareColorSize($product);
        return $product->getColor();
    }

    /**
     * @param Mage_Sales_Model_Quote_Item|Mage_Sales_Model_Order_Item $item
     * @return mixed
     */
    public function getIsGift($item = NULL)
    {
        $item or $item = $this->getItem();
        return $this->_getHelper()->getIsGift($item);
    }

    public function getMessage()
    {
        return $this->_getHelper()->getMessage($this->getItem()->getBuyRequest());
    }

    /**
     * @param Mage_Sales_Model_Quote_Item|Mage_Sales_Model_Order_Item $item
     * @return Mage_Sales_Model_Quote_Item|Mage_Sales_Model_Order_Item|Mage_Sales_Model_Quote_Item
     */
    public function getQuoteItem($item = NULL)
    {
        $item or $item = $this->getItem();
        return $this->_getHelper()->getQuoteItem($item);
    }

    public function getRowSpan()
    {
        $method = $this->getItem() instanceof Mage_Sales_Model_Quote_Item ?
            'getChildren' : 'getChildrenItems';
        return count($this->getItem()->$method()) + 1;
    }

    public function getSize($product)
    {
        $this->_prepareColorSize($product);
        return $product->getSize();
    }
}
