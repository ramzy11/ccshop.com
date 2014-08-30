<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @category    Mage
 * @package     Mage_Customer
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer dashboard block
 *
 * @category   Gri
 * @package    Gri_Vip
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Gri_Vip_Block_Account_Membership extends Mage_Core_Block_Template
{
    const START = '0.0';
	const STEPONE = '22.0';
	const STEPTWO = '54.0';
    const END = '99.5';
    const TIME_RULE = 'y-MM-dd H:m:s'; //2012-01-01 01:01:01
    const EXPIRED_DATE_RULE = 'dd/MM/y';// 29/11/2012

    protected $_gainedPoints = array();

    /**
     * @return Gri_Vip_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('gri_vip');
    }

    public function getVipClass()
    {
        return $this->__('%s', $this->__($this->getGroupCode()));
    }

    public function getVipDiscount()
    {
        return ($level = $this->_getHelper()->getCustomerVipLevel($this->getCustomer())) == 'general' ?
            '0' : $this->_getHelper()->getLevelDiscount($level) . '%';
    }

    /**
     *  get redeem points
     *
     *  @return  int
     */
    public function getRedeemPoints()
    {
        //$pointsBalance = $this->getRewardInstance($this->getCustomer())->getPointsBalance();
		$pointsBalance = $this->getVipPk()->getVipPoint();
        return (int)$pointsBalance;
    }

    /**
     *   get  expired date of vip
     *
     *  @return  string
     */
    public function getVipExpirationDate()
    {
        $customerId = $this->getCustomer()->getId();
        //$onlineVip = Mage::getSingleton('gri_vip/relation_online')->load($customerId, 'customer_id');
        $onlineVip = Mage::getSingleton('gri_vip/offline_pk')->load($customerId, 'customer_id');

        //if ($onlineVip->getId() && $onlineVip->getState()) {
        if ($onlineVip->getId()) {
            //$annual_time = $onlineVip->getAnnualTime();
            $annual_time = $onlineVip->getExpiryDate();
            $annual_time = strtotime($annual_time);
            $date = Mage::getSingleton('core/locale')->date($annual_time)->toString(self::EXPIRED_DATE_RULE);

            return $date;
        }

        return '*/*/';
    }

    /**
     *  get  %  left
     */
    public function getLeftPercentPosition()
    {
        return $this->_getPercentPosition($this->getVipPk()->getVipPoint());
    }

    public function getDynamicPoints()
    {
        if ($this->_getHelper()->getCustomerVipLevel($this->getCustomer()) == 'gold') {
            $points = $this->getPointsOfGoldUpgrade();
        }
        else if ($this->_getHelper()->getCustomerVipLevel($this->getCustomer()) == 'silver') {
            $points = $this->getGainedPoints('12');
        }
        else {
            $points = $this->getGainedPoints('3');
        }

        return $points;
    }

    /**
     * Get next level to upgrade to
     */
    public function getLevelUpgradeTo()
    {
        $groupId = $this->getCustomer()->getGroupId();
        $customerGroup = clone $this->getCustomer()->getGroup();
        //if ($groupId == $gold = $this->_getHelper()->getGroupIdByVipLevel('gold')) {
        if ($groupId == $platinum = $this->_getHelper()->getGroupIdByVipLevel('platinum')) {
            return $this->__('You are already %s Member!', $this->_getHelper()->__($customerGroup->getCode()));
        }
		elseif ($groupId == $gold = $this->_getHelper()->getGroupIdByVipLevel('gold')) {
			$nextLevel = $platinum;
		}
		elseif($groupId == $silver = $this->_getHelper()->getGroupIdByVipLevel('silver')) {
	        $nextLevel = $gold;
		}
		else
		{
			$nextLevel = $silver;
		}

		
        $nextLevel = $customerGroup->unsetData()->load($nextLevel)->getCode();
        //return $this->_getHelper()->__('Points to upgrade to %s', $this->_getHelper()->__($nextLevel));
		return $this->_getHelper()->__($nextLevel);
    }

    protected function _getPercentPosition($pointsUsed)
    {
		$type = 0;
		if($pointsUsed > $this->getPointsOfSilverUpgrade() && $pointsUsed <= $this->getPointsOfGoldUpgrade())
		{
			$type = 1;
		}
		if($pointsUsed > $this->getPointsOfGoldUpgrade() && $pointsUsed <= $this->getPointsOfPlatinumUpgrade())
		{
			$type = 2;
		}
		if($pointsUsed > $this->getPOintsOfPlatinumUpgrade())
		{
			$type = 3;
		}
		
		switch($type)
		{
			case 0:
			$delta = $pointsUsed / $this->getPointsOfSilverUpgrade() * self::STEPONE;
			$left_percent = max($delta, self::START);
			break;

			case 1:
			$delta = ($pointsUsed - $this->getPointsOfSilverUpgrade()) / ($this->getPointsOfGoldUpgrade() - $this->getPointsOfSilverUpgrade()) * (self::STEPTWO - self::STEPONE);
			$left_percent = self::STEPONE + $delta;
			break;

			case 2:
			$delta = ($pointsUsed - $this->getPointsOfGoldUpgrade()) / ($this->getPointsOfPlatinumUpgrade() - $this->getPointsOfGoldUpgrade()) * (self::END - self::STEPTWO);
			$left_percent = self::STEPTWO + $delta;
			break;

			case 3:
			$left_percent = self::END;
			break;
		}

        //output
        return $left_percent;
    }

    /**
     * Get reward points gained in months
     * @param  $months
     * @return int
     */
    public function getGainedPoints($months)
    {
        /*if (!isset($this->_gainedPoints[$months])) {
            $from = Mage::getSingleton('core/locale')->date()->subMonth($months)->toString(self::TIME_RULE);
            $to = Mage::getSingleton('core/locale')->date()->toString(self::TIME_RULE);

            $customerId = $this->getCustomer()->getId();
            $sql = "select `b`.`points_delta` from `gri_reward` a  inner  join `gri_reward_history`  b  on `a`.`reward_id`=`b`.`reward_id`  where `b`.`created_at`>='" .
                $from . "' and  `b`.`created_at`<='" . $to . "'  and `a`.`customer_id`='" . $customerId . "'";
            $read = Mage::getSingleton('core/resource')->getConnection('core_read');
            $rows = $read->query($sql)->fetchAll(PDO::FETCH_ASSOC);

            $points = 0;
            foreach ($rows as $r) {
                if ($r['points_delta'] > 0) {
                    $points += $r['points_delta'];
                }
            }
            $this->_gainedPoints[$months] = $points;
        }
        return $this->_gainedPoints[$months];*/
		$vipPk = $this->getVipPk();
		return $vipPk->getVipPoint();
    }

    /**
     * Get reward points to be gained to upgrade to next VIP level
     *
     * @return integer
     */
    public function getPointsToUpgrade()
    {
        $incrementPoints = $this->getGainedPoints(12);
        if ($this->_getHelper()->getCustomerVipLevel($this->getCustomer()) == 'platinum') {
			return '';
		}
        elseif ($this->_getHelper()->getCustomerVipLevel($this->getCustomer()) == 'gold') {
            $targetPoints = $this->getPointsOfPlatinumUpgrade();
        } elseif ($this->_getHelper()->getCustomerVipLevel($this->getCustomer()) == 'silver') {
            $targetPoints = $this->getPointsOfGoldUpgrade();
        } else {
            $targetPoints = $this->getPointsOfSilverUpgrade();
        }
        return max(0, $targetPoints - $incrementPoints);
    }

    public function getPointIncrement()
    {
        $reward = $this->getRewardInstance($this->getCustomer());
        $points = 0;
        if ($reward->getId()) {
            $points = $reward->getPointsIncrement();
        }
        return (int)$points;
    }

    /**
     * @return Gri_Customer_Model_Customer
     */
    public function getCustomer()
    {
        if ($this->getData('customer') === NULL) {
			$customer = Mage::getSingleton('customer/session')->getCustomer();
            $this->setData('customer', $customer);
			$this->setData('vip_pk', Mage::getSingleton('gri_vip/offline_pk')->load($customer->getId(),'customer_id'));
			unset($customer);
        }
        return $this->getData('customer');
    }

	public function getVipPk()
	{
		if($this->getData('vip_pk') === NULL)
		{
			if($this->getData('customer') === NULL)
			{
				$this->setData('customer',Mage::getSingleton('customer/session')->getCustomer());			
			}
			$this->setData('vip_pk',Mage::getSingleton('gri_vip/offline_pk')->load($this->getCustomer()->getId(),'customer_id'));
		}

		return $this->getData('vip_pk');
	}

    public function getGroupCode()
    {
        return $this->getCustomer()->getGroupCode();
    }

    public function getPointsOfSilverUpgrade()
    {
        $points = Mage::getStoreConfig(Gri_Vip_Helper_Data::CONFIG_PATH_VIP_REQUIREMENT_SILVER_UPGRADE);
        return abs($points);
    }

    public function getPointsOfGoldUpgrade()
    {
        $points = Mage::getStoreConfig(Gri_Vip_Helper_Data::CONFIG_PATH_VIP_REQUIREMENT_GOLD_UPGRADE);
        return abs($points);
    }

	public function getPointsOfPlatinumUpgrade()
	{
		$points = Mage::getStoreConfig(Gri_Vip_Helper_Data::CONFIG_PATH_VIP_REQUIREMENT_PLATINUM_UPGRADE);
		return abs($points);
	}

    /**
     * @param Mage_Customer_Model_Customer $customer
     * @return Gri_Reward_Model_Reward
     */
    public function getRewardInstance(Mage_Customer_Model_Customer $customer)
    {
        if ($customer->getData('reward_instance') === NULL) {
            $reward = Mage::getModel('gri_reward/reward')->load($customer->getId(), 'customer_id');
            $customer->setData('reward_instance', $reward);
        }
        return $customer->getData('reward_instance');
    }

	public function getUpgradeMessage()
	{
		$ret = "";

		$point = $this->getPointsToUpgrade();
		$grade = $this->_getHelper()->getCustomerVipLevel($this->getCustomer());
		$customerGroup = clone $this->getCustomer()->getGroup();

		if($grade == 'platinum')
		{
			$ret = $this->__('You are already %s Member!', $this->_getHelper()->__($customerGroup->getCode()));
		}
		else
		{
			$nextGrade = array('grey'=>$this->__('Silver'),'general'=>$this->__('Silver'),'silver'=>$this->__('Gold'),'gold'=>$this->__('Platinum'));
			$ret = $this->__('%1$s points to upgrade to %2$s',$point,$nextGrade[$grade]);
		}

		return $ret;
	}
}
