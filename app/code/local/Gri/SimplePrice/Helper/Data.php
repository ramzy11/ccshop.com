<?php

class Gri_SimplePrice_Helper_Data extends Mage_Core_Helper_Abstract
{
    CONST CONFIG_PATH_SIMPLE_PRICE_ENABLED = 'simpleprice/settings/enabled';

    protected function _getSpecialPrice(Mage_Catalog_Model_Product $product)
    {
        return $product->getPriceModel()->calculateSpecialPrice($product->getPrice(), $product->getSpecialPrice(),
            $product->getSpecialFromDate(), $product->getSpecialToDate(), $product->getStore());
    }

    public function getSimplePrices(Mage_Catalog_Model_Product $product)
    {
        if ($product->getSimplePrices() === NULL) {
            /* @var $type Mage_Catalog_Model_Product_Type_Configurable */
            $type = $product->getTypeInstance(TRUE);
            $refPrice = $product->getData('final_price') * 1;
            $prices = array();
            /* @var $childProduct Mage_Catalog_Model_Product */
            if ($type instanceof Mage_Catalog_Model_Product_Type_Configurable) foreach ($type->getUsedProducts(NULL, $product) as $childProduct) {
                if (!$childProduct->isSalable()) continue;
                if (($specialPrice = $this->_getSpecialPrice($childProduct) * 1) > 0 &&
                    $specialPrice < $refPrice
                ) {
                    $prices[$childProduct->getId()] = $specialPrice;
                } else {
                    $prices[$childProduct->getId()] = $refPrice;
                }
            }
            $product->setSimplePrices($prices);
        }
        return $product->getSimplePrices();
    }

    public function isEnabled()
    {
        return Mage::getStoreConfig(self::CONFIG_PATH_SIMPLE_PRICE_ENABLED);
    }

    public function setUseDummyFinalPrice(Mage_Catalog_Model_Product $product, $value = TRUE)
    {
        if ($this->isEnabled() &&
            $product->getTypeInstance() instanceof Mage_Catalog_Model_Product_Type_Configurable
        ) {
            $product->setUseDummyFinalPrice($value);
        }
    }

    public function updateSimplePrice(Mage_Catalog_Model_Product $product)
    {
        /* @var $type Mage_Catalog_Model_Product_Type_Configurable */
        if ($this->isEnabled() && $product->getId() && !$product->getSimplePricePriceCalculated()) {
            $refPrice = $product->getData('final_price');
            if ($prices = $this->getSimplePrices($product)) {
                $minimalPrice = min($prices);
                if ($options = $product->getCustomOptions()) {
                    if (isset($options['simple_product']) &&
                        isset($prices[$options['simple_product']->getProductId()]) &&
                        ($finalPrice = $prices[$options['simple_product']->getProductId()]) &&
                        $finalPrice < $refPrice
                    ) {
                        $product->setFinalPrice($finalPrice);//->setSpecialPrice($finalPrice);
                    }
                } else {
                    $product->setFinalPrice($finalPrice = min(max($prices), $refPrice));//->setSpecialPrice($finalPrice);
                }
                !$product->getMinimalPrice() || ($product->getMinimalPrice() * 1 > $minimalPrice) and
                    $product->setMinimalPrice($minimalPrice);
            }
        }
        return $this;
    }
}
