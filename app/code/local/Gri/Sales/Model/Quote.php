<?php
class Gri_Sales_Model_Quote extends Mage_Sales_Model_Quote
{

    /**
     * valid  if  enough  reward points  for  gift product
     */
    public function validEnoughRewardPointsForGifts()
    {
        $success = FALSE;

        /* @var Mage_Sales_Model_Quote */
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        $allItems = $quote->getAllItems();

        $customer_balance_points = $this->_getCustomerBalancePoints();

        $totalRewardPoints = 0;
        foreach ($allItems as $item) {
            /* @var   Mage_Sales_Model_Quote_Item */
            $product = $item->getProduct()->load(NULL);

            $item->setAttributeSetId($product->getAttributeSetId());
            $item->setTypeId($product->getTypeId());
            $item->setRewardPoints($product->getRewardPoints());

            // gift  product
            $giftsAttributeSetId = $this->getAttributeSetIdByName('gifts');
            if ($giftsAttributeSetId &&
                $giftsAttributeSetId == $item->getAttributeSetId()
            ) {
                $totalRewardPoints += $product->getRewardPoints() * $item->getQty();
            }

            // gift card
            if ($product->getTypeId() == 'giftcard') {
                $rate = $product->getCustomOption('giftcard_amount')->getValue() * $item->getQty();
                $purchase_points = $this->_getRateToPoints($rate);
                $totalRewardPoints += $purchase_points;
            }
        }

        if ($customer_balance_points && $totalRewardPoints &&
            $customer_balance_points >= $totalRewardPoints
        ) {
            $success = FALSE;
        }

        if ($totalRewardPoints && $customer_balance_points < $totalRewardPoints) {
            $success = TRUE;
        }
        return $success;
    }

    protected function _getProductTypeId($typeCode)
    {
        $typeCode = strtolower($typeCode);
        return Mage::getSingleton('catalog/product_flat_indexer')->load($typeCode, 'type_id')->getEntityId();
    }

    /**
     * Return rate to convert currency amount to points
     * @param  float $rate
     * @return Gri_Reward_Model_Reward_Rate
     */
    protected function _getRateToPoints($rate)
    {
        $rate_to_points = Mage::getSingleton('gri_reward/reward')->getRateToPoints();
        return intval($rate * $rate_to_points->getPoints() / $rate_to_points->getCurrencyAmount());
    }

    public function getAttributeSetIdByName($name)
    {
        /* @var $config Mage_Catalog_Model_Config */
        $config = Mage::getSingleton('catalog/config');
        return $config->getAttributeSetId('catalog_product', $name);
    }

    /**
     *  get customer's reward points in balance
     *
     * @return  int|0
     */
    protected function _getCustomerBalancePoints()
    {
        $customer = $this->getCustomer();
        $reward = Mage::getSingleton('gri_reward/reward')->load($customer->getEntityId(), 'customer_id');

        return $reward->getPointsBalance();
    }

    /**
     *  getter
     *
     * @return   int | 0
     */
    public function getTotalRewardPointsForGiftsInQuote()
    {
        /* @var  Mage_Sales_Model_Quote */
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        $items = $quote->getAllItems();
        $points = 0;
        foreach ($items as $item) {
            $points += $this->calculateRewardPointsForGifts($item, $quote);
        }

        return $points;
    }

    /**
     * @param  object $item
     * @param  object $quote
     *
     * @return int|0
     */
    public function calculateRewardPointsForGifts($item, $quote)
    {
        $points = 0;
        if ($item->getQuoteId() == $quote->getId()) {
            $product = $item->getProduct();

            $quoteItem = Mage::getSingleton('sales/quote_item')->load($item->getId());
            if ($quoteItem->getId()) {
                // gift card
                $giftcard = Mage::getSingleton('gri_giftcard/catalog_product_price_giftcard');
                if ($product->getTypeId() == 'giftcard') {
                    $points += $giftcard->getRewardPoints($item->getQty(), $product);
                }

                // gift product
                $giftproductAttributeSetId = $this->getAttributeSetIdByName('gifts');
                if ($product->getAttributeSetId() == $giftproductAttributeSetId) {
                    $product = $product->load(NULL);
                    $points += $product->getRewardPoints();
                }
            }
        }

        return $points;
    }
}
