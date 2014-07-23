<?php

class Gri_CatalogCustom_Block_Product_View_Share extends Mage_Catalog_Block_Product_View
{

    protected $_product;

    /**
     * Retrieve product object
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        if (!$this->_product) {
            if (Mage::registry('current_product')) {
                $this->_product = Mage::registry('current_product');
            } else {
                $this->_product = Mage::getSingleton('catalog/product');
            }
        }
        return $this->_product;
    }

    /**
     * Set product object
     *
     * @param Mage_Catalog_Model_Product $product
     * @return Mage_Catalog_Block_Product_View_Options
     */
    public function setProduct(Mage_Catalog_Model_Product $product = NULL)
    {
        $this->_product = $product;
        return $this;
    }

    public function filterStr($str)
    {
        return json_encode(trim($str));
    }
}
