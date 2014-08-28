<?php
/**
 * Relation Offline model
 *
 * @category    Gri
 * @package     Gri_Vip
 */
class  Gri_Vip_Model_Mysql4_Relation_Offline extends Mage_Core_Model_Resource_Db_Abstract
{

    /**
     * Internal constructor
     */
    protected function _construct()
    {
        $this->_init('gri_vip/relation_offline', 'id');
    }

    /**
     * @return Gri_Vip_Helper_Data
     */
    public function getHelper()
    {
        return Mage::helper('gri_vip');
    }

    /**
     * @param integer $state
     * @param null|Gri_Vip_Model_Relation_Offline $offlineVip
     * @return Gri_Vip_Model_Mysql4_Relation_Offline
     */
    public function resetState($state = 0, $offlineVip = NULL)
    {
        if ($offlineVip instanceof Gri_Vip_Model_Relation_Offline) $offlineVip->setState($state);
        else {
            $this->_getWriteAdapter()->update($this->getMainTable(), array('state' => $state));
        }
        return $this;
    }

    public function syncCustomerGroups()
    {
        $GroupIdGeneral = $this->getHelper()->getGroupIdByVipLevel('general');
        $GroupIdOfflineVip = $this->getHelper()->getGroupIdByVipLevel('offlinevip');
        $sql = "UPDATE `{$this->getTable('customer/entity')}` e
JOIN `{$this->getMainTable()}` o ON o.`customer_id`=e.`entity_id`
SET e.`group_id` = :to_group_id
WHERE o.`state` = :state
AND e.`group_id` = :from_group_id";
        $this->_getWriteAdapter()->query($sql, array(
            'from_group_id' => $GroupIdOfflineVip,
            'to_group_id' => $GroupIdGeneral,
            'state' => 0,
        ));
        $this->_getWriteAdapter()->query($sql, array(
            'from_group_id' => $GroupIdGeneral,
            'to_group_id' => $GroupIdOfflineVip,
            'state' => 1,
        ));
        return $this;
    }
}
