<?php
/**
 * Magento Gri Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Gri Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/gri-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Gri
 * @package     Gri_Reward
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/gri-edition
 */

/**
 * Reward action for converting spent money to points
 *
 * @category    Gri
 * @package     Gri_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Gri_Reward_Model_Action_OrderExtra extends Gri_Reward_Model_Action_Abstract
{
    /**
     * Quote instance, required for estimating checkout reward (order subtotal - discount)
     *
     * @var Mage_Sales_Model_Quote
     */
    protected $_quote = null;

    /**
     * Return action message for history log
     *
     * @param array $args Additional history data
     * @return string
     */
    public function getHistoryMessage($args = array())
    {
        $incrementId = isset($args['increment_id']) ? $args['increment_id'] : '';
        return Mage::helper('gri_reward')->__('Earned points for order #%s.', $incrementId);
    }

    /**
     * Setter for $_entity and add some extra data to history
     *
     * @param Varien_Object $entity
     * @return Gri_Reward_Model_Action_Abstract
     */
    public function setEntity($entity)
    {
        parent::setEntity($entity);
        $this->getHistory()->addAdditionalData(array(
            'increment_id' => $this->getEntity()->getIncrementId()
        ));
        return $this;
    }

    /**
     * Quote setter
     *
     * @param Mage_Sales_Model_Quote $quote
     * @return Gri_Reward_Model_Action_OrderExtra
     */
    public function setQuote(Mage_Sales_Model_Quote $quote)
    {
        $this->_quote = $quote;
        return $this;
    }

    /**
     * Retrieve points delta for action
     *
     * @param int $websiteId
     * @return int
     */
    public function getPoints($websiteId)
    {
        if (!Mage::helper('gri_reward')->isOrderAllowed($this->getReward()->getWebsiteId())) {
            return 0;
        }
        if ($this->_quote) {
            $quote = $this->_quote;
            // known issue: no support for multishipping quote
            $address = $quote->getIsVirtual() ? $quote->getBillingAddress() : $quote->getShippingAddress();
            // use only money customer spend - shipping & tax
            $monetaryAmount = $quote->getBaseGrandTotal()
                - $address->getBaseShippingAmount()
                - $address->getBaseTaxAmount();
            $monetaryAmount = $monetaryAmount < 0 ? 0 : $monetaryAmount;
        } else {
            $monetaryAmount = $this->getEntity()->getBaseTotalPaid()
                - $this->getEntity()->getBaseShippingAmount()
                - $this->getEntity()->getBaseTaxAmount();
        }
        
        !isset($quote) && $quote =  $this->getEntity() ;
        $monetaryAmount = $this->subMonetaryAmount($monetaryAmount,$quote); 
        
        $pointsDelta = $this->getReward()->getRateToPoints()->calculateToPoints((float)$monetaryAmount);
        
        return $pointsDelta;
    }
    
    /**
     *  Check whether rewards can be added for action
     *  Checking for the history records is intentionaly omitted
     *
     *  @return bool
     *
     */
    public function canAddRewardPoints()
    {
        return parent::canAddRewardPoints()
           && Mage::helper('gri_reward')->isOrderAllowed($this->getReward()->getWebsiteId());
    }
    
    
    /**
     * Point accumulation based on HK$1 spending = 1 point (With the exception of discounted items with 50% off OR more than 50%.)
     * @return Float
     */
    public  function subMonetaryAmount($monetaryAmount,$quote){
        $items = $quote->getAllItems();
        foreach($items as $item){
           $product = $item->getProduct(); 
           $rowTotal = $item->getRowTotal() + $item->getTaxAmount() + $item->getHiddenTaxAmount() + $item->getWeeeTaxAppliedRowAmount() - $item->getDiscountAmount();
           if( $item->getQtyOrdered() && ($rowTotal/$item->getQtyOrdered() < $product->getPrice()* 0.5) ){
              $monetaryAmount  -=  $rowTotal ;    
           }
        }
        
        return $monetaryAmount ;
    }
        
}