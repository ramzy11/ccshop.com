<?php

class Gri_CatalogCustom_Block_Product_View_Description extends Mage_Catalog_Block_Product_View_Description
{

    public function setProduct(Mage_Catalog_Model_Product $product)
    {
        $this->_product = $product;
        return $this;
    }
}
