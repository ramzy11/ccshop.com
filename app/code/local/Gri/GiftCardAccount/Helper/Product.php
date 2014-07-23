<?php

class Gri_GiftCardAccount_Helper_Product extends Mage_Core_Helper_Abstract
{

    /**
     * @param Gri_CatalogCustom_Model_Product $product
     * @param Mage_Customer_Model_Customer $customer
     * @return Gri_GiftCardAccount_Model_Giftcardaccount
     */
    public function generateGiftCardFromProduct($product, Mage_Customer_Model_Customer $customer)
    {
        $isRedeemable = Mage::getStoreConfig(Gri_GiftCard_Model_Giftcard::XML_PATH_IS_REDEEMABLE);

        /* @var $giftCardAccount Gri_GiftCardAccount_Model_Giftcardaccount */
        $giftCardAccount = Mage::getModel('gri_giftcardaccount/giftcardaccount');
        $giftCardAccount->setStatus(Gri_GiftCardAccount_Model_Giftcardaccount::STATUS_ENABLED)
            ->setWebsiteId($product->getStore()->getWebsiteId())
            ->setBalance($product->getAmount())
            ->setLifetime($product->getGiftcardLifetime())
            ->setIsRedeemable($isRedeemable)
            ->setProduct($product)
            ->setRecipientName($customer->getName())
            ->setRecipientEmail($customer->getEmail())
            ->setRecipientStore($product->getStore())
            ->save();
        $giftCardAccount->sendEmail();
        return $giftCardAccount;
    }

    /**
     * @param Gri_CatalogCustom_Model_Product $product
     * @param Gri_Reward_Model_Reward $reward
     * @return Gri_GiftCardAccount_Model_Giftcardaccount|string
     */
    public function redeemGiftCardWithRewardPoints($product, Gri_Reward_Model_Reward $reward)
    {
        if (!$product->isGiftCard()) return $this->__('Invalid gift card product.');
        $balance = $reward->getPointsBalance();
        $amounts = array();
        foreach ($product->getGiftcardAmounts() as $amount) {
            $amounts[] = Mage::app()->getStore()->roundPrice($amount['website_value']);
        }
        $product->setAmount(min($amounts));
        $points = $product->getRewardPoints();

        if ($balance >= $points) {
            try {
                $giftCardAccount = $this->generateGiftCardFromProduct($product, $reward->getCustomer());
                $reward->setActionEntity($product)
                    ->setPointsDelta(-$points)
                    ->setAction(Gri_Reward_Model_Reward::REWARD_ACTION_REDEEM_GIFTCARD);
                $reward->updateRewardPoints();
                return $giftCardAccount;
            } catch (Exception $e) {
                Mage::logException($e);
                return $e->getMessage();
            }
        } else {
            return $this->__('Insufficient reward points.');
        }
    }
}
