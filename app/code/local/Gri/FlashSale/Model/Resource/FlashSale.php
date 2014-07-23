<?php

class Gri_FlashSale_Model_Resource_FlashSale extends Mage_Core_Model_Resource_Db_Abstract
{

    protected function _construct()
    {
        $this->_init('gri_flashsale/flashsale', 'flash_sale_id');
    }

    public function activate(Gri_FlashSale_Model_FlashSale $object)
    {
        if (!$object->getOrigData('is_active') && $id = (int)$object->getId()) {
            $write = $this->_getConnection('write');
            $write->update($this->getMainTable(), array('is_active' => 0), array(
                '`flash_sale_id` < ' . $id . ' OR `flash_sale_id` > ' . $id,
            ));
        }
        return $this;
    }

    public function getActiveFlashSaleId()
    {
        /* @var $now Mage_Core_Model_Date */
        $now = Mage::getSingleton('core/date');
        return $this->getReadConnection()
            ->fetchOne("SELECT `{$this->getIdFieldName()}`
FROM `{$this->getMainTable()}`
WHERE `is_active` = 1
AND `start` <= :now
AND `end` >= :now", array(':now' => $now->gmtDate()));
    }
}
