<?php

class Gri_Affiliate_Model_Resource_Order extends Mage_Core_Model_Resource_Db_Abstract
{

    protected function _construct()
    {
        $this->_init('gri_affiliate/order', 'entity_id');
    }

    /**
     * @param string $code Affiliate code
     * @return false|Gri_Affiliate_Model_Affiliate_Abstract
     */
    public function getAffiliateModel($code)
    {
        $class = (string)Mage::getConfig()->getNode('affiliate/' . $code . '/class');
        $result = $class ? Mage::getModel($class) : FALSE;
        if (is_object($result) && !$result instanceof Gri_Affiliate_Model_Affiliate_Abstract) {
            unset($result);
            $result = FALSE;
            Mage::log('Invalid affiliate model: ' . get_class($result), Zend_Log::WARN);
        }
        return $result;
    }

    public function getCountOfSentOrder(Gri_Affiliate_Model_Order $object)
    {
        $select = $this->_getReadAdapter()->select();
        $select->from(array('t' => $this->getMainTable()), 'COUNT(*)')
            ->where($select->getAdapter()->quoteIdentifier('t.affiliate') . ' = ?', $object->getAffiliate())
            ->where($select->getAdapter()->quoteIdentifier('t.hash') . ' = ?', $object->getHash())
            ->where($select->getAdapter()->quoteIdentifier('t.is_sent') . ' = ?', 1)
        ;
        return $select->query()->fetchColumn();
    }
}
