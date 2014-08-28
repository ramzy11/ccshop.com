<?php

class Gri_Vip_Helper_Data extends Mage_Core_Helper_Abstract
{
    const CONFIG_PATH_VIP_CUSTOMER_GROUPS = 'gri_vip/customer_groups/';
    const CONFIG_PATH_VIP_REQUIREMENT = 'gri_vip/requirement/';
    const CONFIG_PATH_VIP_REQUIREMENT_SILVER_UPGRADE = 'gri_vip/requirement/silver_upgrade';
    const CONFIG_PATH_VIP_REQUIREMENT_GOLD_UPGRADE = 'gri_vip/requirement/gold_upgrade';
    const CONFIG_PATH_VIP_REQUIREMENT_PLATINUM_UPGRADE = 'gri_vip/requirement/platinum_upgrade';
    const CONFIG_PATH_VIP_DISCOUNT = 'gri_vip/discount/';
    const  CONFIG_PATH_ENABLE_OFFLINEVIP = 'gri_vip/general/enabled_offlinevip';
    const OFFLINEVIP_LEVEL = 'offlinevip';
    const SILVER_LEVEL = 'silver';
    const GOLD_LEVEL = 'gold';
    public function getCustomerVipLevel(Mage_Customer_Model_Customer $customer)
    {
        if ($customer->getData('vip_level') === NULL) {
            $levels = array(
                $this->getGroupIdByVipLevel($level = 'gold') => $level,
                $this->getGroupIdByVipLevel($level = 'silver') => $level,
                $this->getGroupIdByVipLevel($level = 'platinum') => $level,
                $this->getGroupIdByVipLevel($level = 'offlinevip') => $level,
                $this->getGroupIdByVipLevel($level = 'general') => $level,
            );
            $customer->setData('vip_level', isset($levels[$customer->getGroupId()]) ?
                $levels[$customer->getGroupId()] : 'general');
        }
        return $customer->getData('vip_level');
    }

    /**
     * Get group id by VIP level
     * @param string $level
     * @return integer
     */
    public function getGroupIdByVipLevel($level)
    {
        return Mage::getStoreConfig(self::CONFIG_PATH_VIP_CUSTOMER_GROUPS . $level);
    }
    
    
    /**
     * Get VIP level  by  group id
     * @param string $customerGroupId
     * @return string 
     */
    public function getVipLevelByGroupId($customerGroupId)
    {
          //return Mage::getStoreConfig(self::CONFIG_PATH_VIP_CUSTOMER_GROUPS . $level);
          $silverVipId = Mage::getStoreConfig(self::CONFIG_PATH_VIP_CUSTOMER_GROUPS . 'silver') ;
          $offlineVipId = Mage::getStoreConfig(self::CONFIG_PATH_VIP_CUSTOMER_GROUPS . 'offlinevip') ;
          $goldVipId = Mage::getStoreConfig(self::CONFIG_PATH_VIP_CUSTOMER_GROUPS . 'gold') ;
          $platinumVipId = Mage::getStoreConfig(self::CONFIG_PATH_VIP_CUSTOMER_GROUPS . 'platinum') ;
          
          if( $silverVipId == $customerGroupId){
               return  'silver' ; 
          }
          if( $offlineVipId == $customerGroupId){
               return 'offlinevip' ;
          }
          if($goldVipId == $customerGroupId){
               return 'gold';               
          }
          if($platinumVipId == $customerGroupId){
				return 'platinum';
			}
          return '' ;
    }

    /**
     * Get discount by VIP level
     * @param string $level
     * @return integer
     */
    public function getLevelDiscount($level)
    {
        return Mage::getStoreConfig(self::CONFIG_PATH_VIP_DISCOUNT . $level);
    }

    /**
     * Get VIP classification requirement
     *
     * @param string $level
     * @return array
     */
    public function getLevelRequirement($level)
    {
        $entry = Mage::getStoreConfig(self::CONFIG_PATH_VIP_REQUIREMENT . $level . '_entry');
        $upgrade = Mage::getStoreConfig(self::CONFIG_PATH_VIP_REQUIREMENT . $level . '_upgrade');
        $renewal = Mage::getStoreConfig(self::CONFIG_PATH_VIP_REQUIREMENT . $level . '_renewal');
        $data = array(
            'entry_requirement' => $entry,
            'upgrade_requirement' => $upgrade,
            'annual_renewal_requirement' => $renewal,
        );
        return $data;
    }

    /**
     * Check if a customer is VIP
     * @param Mage_Customer_Model_Customer $customer
     * @return bool
     */
    public function isVip(Mage_Customer_Model_Customer $customer)
    {
        return $customer->getId() && $this->getCustomerVipLevel($customer) != 'general';
    }

    /**
     *  Getter  OfflineVIP  is  enable  or  disable
     *
     **/
    public  function   getEnableOfflineVIP(){
        return   Mage::getStoreConfig(self::CONFIG_PATH_ENABLE_OFFLINEVIP);
    }

    
}
