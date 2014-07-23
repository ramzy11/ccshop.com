<?php
/**
 * Relation Offline model
 *
 * @category    Gri
 * @package     Gri_Vip
 * @method Gri_Vip_Model_Mysql4_Relation_Offline getResource()
 * @method integer getState()
 * @method Gri_Vip_Model_Relation_Offline setState(integer)
 */
class  Gri_Vip_Model_Relation_Offline extends Mage_Core_Model_Abstract
{

    protected function _beforeSave()
    {
        $now = Varien_Date::now();
        $this->getCreateTime() or $this->setCreateTime($now);
        $this->setUpdateTime($now);
        return parent::_beforeSave();
    }

    protected function _construct()
    {
        parent::_construct();
        $this->_init('gri_vip/relation_offline');
    }

    /**
     * Get offline vip id by customer id
     * @param integer $customerId
     * @return string
     */
    public function getVipId($customerId)
    {
        return $this->load($customerId, 'customer_id')->getCardNo();
    }
}
