<?php

class Gri_SalesRule_Model_Quote_Item extends Mage_Sales_Model_Quote_Item
{

    public function getProduct()
    {
        if (($product = $this->_getData('product')) instanceof Mage_Catalog_Model_Product
            && $product->getSkipResetFinalPrice()
        ) return $product;
        return parent::getProduct();
    }
}
