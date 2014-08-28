<?php
/**
 * Relation  Online model
 *
 * @category    Gri
 * @package     Gri_Vip
 */
class  Gri_Vip_Model_Mysql4_Relation_Online extends Mage_Core_Model_Resource_Db_Abstract
{

    protected function _construct()
    {
        $this->_init('gri_vip/relation_online', 'id');
    }
}
