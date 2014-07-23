<?php

class Gri_Hamper_Model_Sales_Total_Quote_Subtotal extends Mage_Tax_Model_Sales_Total_Quote_Subtotal
{

    protected function _addGift(Mage_Sales_Model_Quote_Item $item)
    {
        $price = $item->getPrice();
        /* @var $product Mage_Catalog_Model_Product */
        $product = $item->getData('product');
        /* @var $priceModel Gri_Hamper_Model_Product_Price */
        $priceModel = $product->getPriceModel();
        if ($hamperGifts = $priceModel->parseGifts($product, FALSE, TRUE)) {
            foreach ($hamperGifts as $v) {
                if ($price < $v['price']) continue;
                $productId = $v['id'];
                /* @var $gp Mage_Catalog_Model_Product */
                $gp = $v['product'];
                if (!$gp->isSalable()) break;
                /* @var $gift Mage_Sales_Model_Quote_Item */
                $gift = Mage::getModel('sales/quote_item');
                $gift->setQuote($quote = $item->getQuote())->setStoreId($quote->getStoreId())->setProduct($gp)->addOption(array(
                    'code' => 'is_gift',
                    'value' => 1,
                    'product_id' => $productId,
                ))->getProduct();
                $gift->setParentItem($item)->setQty(1);
                $optionData = array(
                    'code' => 'gift_added',
                    'value' => 1,
                    'product_id' => $item->getProductId(),
                );
                if ($added = $item->getOptionByCode($optionData['code'])) $added->setValue(1);
                else $item->addOption($optionData);
                $quote->addItem($gift);
                break;
            }
        }
        return $this;
    }

    protected function _applyDiscount(Mage_Sales_Model_Quote_Item $item)
    {
        $price = $item->getPrice();
        /* @var $product Mage_Catalog_Model_Product */
        $product = $item->getData('product');
        /* @var $priceModel Gri_Hamper_Model_Product_Price */
        $priceModel = $product->getPriceModel();
        if ($hamperDiscount = $priceModel->parseDiscount($product, FALSE)) {
            foreach ($hamperDiscount as $v) {
                if ($price < $v['price']) continue;
                $baseDiscount = $v['discount'];
                substr($baseDiscount, -1) == '%' and $baseDiscount = $price * (100 - $baseDiscount) / 100;
                $discount = $item->getStore()->convertPrice($baseDiscount);
                $basePrice = $item->getBasePrice();
                $rowTotal = $item->getRowTotal();
                $baseRowTotal = $item->getBaseRowTotal();
                $priceInclTax = $item->getPriceInclTax();
                $basePriceInclTax = $item->getBasePriceInclTax();
                $rowTotalInclTax = $item->getRowTotalInclTax();
                $baseRowTotalInclTax = $item->getBaseRowTotalInclTax();
                $item->setConvertedPrice($price - $baseDiscount);
                $item->setPrice($basePrice - $baseDiscount);
                $item->setBasePrice($basePrice - $baseDiscount);
                $item->setRowTotal($rowTotal - $discount);
                $item->setBaseRowTotal($baseRowTotal - $baseDiscount);
                $item->setPriceInclTax($priceInclTax - $discount);
                $item->setBasePriceInclTax($basePriceInclTax - $baseDiscount);
                $item->setRowTotalInclTax($rowTotalInclTax - $discount);
                $item->setBaseRowTotalInclTax($baseRowTotalInclTax - $baseDiscount);
                break;
            }
        }
        return $this;
    }

    protected function _processItem($item, $taxRequest)
    {
        parent::_processItem($item, $taxRequest);
        /* @var $product Mage_Catalog_Model_Product */
        $product = $item->getData('product');
        if ($item instanceof Mage_Sales_Model_Quote_Item &&
            $product->getTypeId() == 'hamper' &&
            $product->getPriceType() == Gri_Hamper_Model_Product_Price::PRICE_TYPE_FIXED
        ) {
            $baseOriginalPrice = 0;
            $originalPrice = 0;
            /* @var $child Mage_Sales_Model_Quote_Item */
            foreach ($item->getChildren() as $child) {
                if ($child->getOptionByCode('is_gift')) continue;
                /* @var $subProduct Mage_Catalog_Model_Product */
                $subProduct = $child->getData('product');
                $child->setBasePrice($basePrice = $subProduct->getPrice())
                    ->setPrice($price = $item->getStore()->convertPrice($basePrice))
                    ->setOriginalPrice($price)
                    ->setBaseOriginalPrice($basePrice);
                $baseOriginalPrice += $basePrice;
                $originalPrice += $price;
            }
            $item->setOriginalPrice($originalPrice)->setBaseOriginalPrice($baseOriginalPrice);
        }
        return $this;
    }

    protected function _removeGift(Mage_Sales_Model_Quote_Item $item)
    {
        $price = $item->getPrice();
        /* @var $product Mage_Catalog_Model_Product */
        $product = $item->getData('product');
        /* @var $priceModel Gri_Hamper_Model_Product_Price */
        $priceModel = $product->getPriceModel();
        $remove = TRUE;
        $gift = FALSE;
        /* @var $childItem Mage_Sales_Model_Quote_Item */
        foreach ($item->getChildren() as $childItem) {
            if ($childItem->getOptionByCode('is_gift')) {
                $gift = $childItem;
                break;
            }
        }
        $gift or $remove = FALSE;
        if ($hamperGifts = $priceModel->parseGifts($product, FALSE, TRUE)) {
            foreach ($hamperGifts as $v) {
                if ($price < $v['price']) continue;
                $productId = $v['id'];
                $remove = $productId != $gift->getProductId();
                break;
            }
        }
        if ($remove || !$gift) {
            $item->getOptionByCode('gift_added')->setValue(0);
            $gift and $gift->isDeleted(TRUE);
            $this->_addGift($item);
        }
        return $this;
    }

    protected function _recalculateParent(Mage_Sales_Model_Quote_Item_Abstract $item)
    {
        /* @var $child Mage_Sales_Model_Quote_Item */
        foreach ($item->getChildren() as $child) {
            if ($child->getOptionByCode('is_gift')) {
                $child->setPrice(0)->setBasePrice(0)->setRowTotal(0)->setBaseRowTotal(0)
                    ->setPriceInclTax(0)->setBasePriceInclTax(0)->setRowTotalInclTax(0)->setBaseRowTotalInclTax(0);
            }
        }
        parent::_recalculateParent($item);
        $item->setBasePrice($item->getPrice());
        /* @var $product Mage_Catalog_Model_Product */
        $product = $item->getData('product');
        if ($item instanceof Mage_Sales_Model_Quote_Item && $product->getTypeId() == 'hamper') {
            $item->setOriginalPrice($item->getPriceInclTax());
            $item->setBaseOriginalPrice($item->getBasePriceInclTax());
            $product->load(NULL);
            $product->setPriceType(Mage_Catalog_Model_Product_Type_Abstract::CALCULATE_PARENT);
            $this->_applyDiscount($item);
            if (!Mage::registry($flag = 'hamper_gift_processed_' . $item->getId())) {
                Mage::register($flag, TRUE);
                if (($added = $item->getOptionByCode('gift_added')) && $added->getValue()) {
                    $this->_removeGift($item);
                } else {
                    $this->_addGift($item);
                }
            }
        }
        return $this;
    }
}
