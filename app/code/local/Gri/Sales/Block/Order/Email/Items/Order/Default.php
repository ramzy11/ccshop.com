<?php

class Gri_Sales_Block_Order_Email_Items_Order_Default extends Mage_Sales_Block_Order_Email_Items_Order_Default
{

    /**
     * @param Mage_Sales_Model_Order_Item $item
     * @return string
     */
    public function getRefNo($item)
    {
        $product = $item->getProduct();
        if ($item->getProductType() == 'configurable' && $item->getChildrenItems()) foreach ($item->getChildrenItems() as $child) {
            $product = $child->getProduct();
            break;
        }
        return $product->getRefNo();
    }
}
