<?php

class Gri_Api_Model_Observer extends Varien_Object
{

    /**
     * @return Gri_Api_Model_Indexer_Product
     */
    protected function _getProductIndexer()
    {
        return Mage::getSingleton('gri_api/indexer_product');
    }

    public function addIsArchiveCondition(Varien_Event_Observer $observer)
    {
        /* @var  Varien_Db_Select $select */
        $select = $observer->getSelect();

        $sql = $select->__toString();
        if( strpos($sql, 'tas_status') && strpos($sql, 'tad_status')){
            $statusCond = Mage::getSingleton('core/resource')->getConnection('core_write')->quoteInto('= ? ', 0);
            $this->_getProductIndexer()->addAttributeToSelect($select, 'is_archived', $observer->getEntityField(), $observer->getStoreField(), $statusCond, true);
        }

        return $this;
    }

    public function postNewOrderToHkAs400(Varien_Event_Observer $observer)
    {
        /** @var $orderInstance Mage_Sales_Model_Order */
        $order = $observer->getOrder();
        if( $order->getStatus() == 'processing'){
            Mage::getModel('gri_api/api_hkAs400_client')->postNewOrderToAS400($order);
        }

        return $this;
    }
}
