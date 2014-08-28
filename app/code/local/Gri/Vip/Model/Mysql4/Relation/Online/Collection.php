<?php
/**
 * Reward collection
 *
 * @category    Gri
 * @package     Gri_Vip
 */
class Gri_Vip_Model_Mysql4_Relation_Online_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{

    protected function _construct()
    {
        $this->_init('gri_vip/relation_online');
    }
}
