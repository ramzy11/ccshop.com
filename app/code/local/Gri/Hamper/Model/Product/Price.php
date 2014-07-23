<?php

class Gri_Hamper_Model_Product_Price extends Mage_Bundle_Model_Product_Price
{
    protected $_productModel;

    /**
     * @param Mage_Catalog_Model_Product $product
     * @return float
     */
    public function getChildPrice($product)
    {
        $price = $product->getPrice();
        if ($superProduct = $product->getSuperProduct() === NULL) {
            $resource = $product->getResource();
            $select = $resource->getReadConnection()->select();
            $superId = $select->from(array('sl' => $resource->getTable('catalog/product_super_link')), 'parent_id')
                ->where('product_id = ?', $product->getId())
                ->limit(1)
                ->query()->fetchColumn();
            $superProduct = FALSE;
            $superId and $superProduct = Mage::getModel('catalog/product')->setStoreId($product->getStoreId())->load($superId);
            $product->setSuperProduct($superProduct);
        }
        if ($superProduct instanceof Mage_Catalog_Model_Product) {
            $price = $superProduct->getPrice();
        }
        return $price;
    }

    /**
     * @return Mage_Catalog_Model_Product
     */
    public function getProductModel()
    {
        if ($this->_productModel === NULL) {
            $this->_productModel = Mage::getModel('catalog/product');
        }
        return $this->_productModel;
    }

    public function getSelectionFinalTotalPrice($bundleProduct, $selectionProduct, $bundleQty, $selectionQty, $multiplyQty = TRUE, $takeTierPrice = TRUE)
    {
        if (is_null($selectionQty)) {
            $selectionQty = $selectionProduct->getSelectionQty();
        }

        if ($bundleProduct->getPriceType() == self::PRICE_TYPE_DYNAMIC) {
            $price = $this->getChildPrice($selectionProduct);
        } else {
            if ($selectionProduct->getSelectionPriceType()) { // percent
                $product = clone $bundleProduct;
                $product->setFinalPrice($this->getPrice($product));
                Mage::dispatchEvent(
                    'catalog_product_get_final_price',
                    array('product' => $product, 'qty' => $bundleQty)
                );
                $price = $product->getData('final_price') * ($selectionProduct->getSelectionPriceValue() / 100);

            } else { // fixed
                $price = $selectionProduct->getSelectionPriceValue();
            }
        }

        if ($multiplyQty) {
            $price *= $selectionQty;
        }

        return min($price,
            $this->_applyGroupPrice($bundleProduct, $price),
            $this->_applyTierPrice($bundleProduct, $bundleQty, $price),
            $this->_applySpecialPrice($bundleProduct, $price)
        );
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @return mixed
     */
    public function parseDiscount(Mage_Catalog_Model_Product $product, $convertPrice = TRUE)
    {
        if ($product->getParsedHamperDiscount() === NULL) {
            $parsed = FALSE;
            if ($discount = $product->getHamperDiscount()) {
                $store = $product->getStore();
                $parsed = array();
                $discount = explode("\n", str_replace("\n\n", "\n", str_replace("\r", "\n", $discount)));
                foreach ($discount as $v) {
                    $v = array_map('trim', explode(',', $v));
                    if (isset($v[1])) {
                        $valueType = (substr($v[1], -1) == '%') ? '%' : '';
                        $price = $v[0] * 1;
                        $convertPrice and $price = $store->convertPrice($price);
                        $value = $v[1] * 1;
                        $convertPrice && !$valueType and $value = $store->convertPrice($value);
                        $parsed[(int)$price] = array(
                            'price' => $price,
                            'discount' => $value . $valueType,
                        );
                    }
                }
                krsort($parsed);
                $parsed = array_values($parsed);
            }
            $product->setParsedHamperDiscount($parsed);
        }
        return $product->getParsedHamperDiscount();
    }

    public function parseGifts(Mage_Catalog_Model_Product $product, $convertPrice = TRUE, $appendProduct = FALSE)
    {
        if ($product->getParsedExtraGifts() === NULL) {
            $productModel = $this->getProductModel();
            $parsed = FALSE;
            if ($gifts = $product->getExtraGifts()) {
                $store = $product->getStore();
                $parsed = array();
                $gifts = explode("\n", str_replace("\n\n", "\n", str_replace("\r", "\n", $gifts)));
                foreach ($gifts as $v) {
                    $v = array_map('trim', explode(',', $v));
                    if (isset($v[1]) && $pid = $product->getIdBySku($v[1])) {
                        $productModel->reset()->setStoreId($product->getStoreId())->load($pid);
                        if (!$productModel->isSalable()) continue;
                        $price = $v[0] * 1;
                        $convertPrice and $price = $store->convertPrice($price);
                        $row = array(
                            'price' => $price,
                            'id' => $pid,
                            'name' => $productModel->getName(),
                        );
                        $appendProduct and $row['product'] = clone $productModel;
                        $parsed[(int)$price] = $row;
                    }
                }
                krsort($parsed);
                $parsed = array_values($parsed);
            }
            $product->setParsedExtraGifts($parsed);
        }
        return $product->getParsedExtraGifts();
    }
}
