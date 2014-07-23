<?php

class Gri_ImportData_Block_Adminhtml_System_Convert_Profile_Run extends Mage_Adminhtml_Block_System_Convert_Profile_Run
{

    protected function _prepareBatchModel()
    {
        if ($this->_batchModelPrepared) {
            return $this;
        }
        parent::_prepareBatchModel();
        if (!strpos(strtolower($this->getProfile()->getActionsXml()), 'gri_importdata/convert_adapter_product')) return $this;
        $batch = $this->_getBatchModel();
        $table = $batch->getBatchImportModel()->getResource()->getMainTable();
        /* @var $connection Varien_Db_Adapter_Pdo_Mysql */
        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
        $readSelect = $connection->select()->from(array('t' => $table))->where('t.batch_id = ?', $batch->getId());
        $data = $readSelect->query()->fetchAll();
        $prices = array();
        $specialPrices = array();
        $obj = new Varien_Object();
        try {
            foreach ($data as $k => $v) {
                $data[$k]['batch_data'] = $batchData = unserialize($v['batch_data']);
                $obj->setData($batchData);
                $key = $obj->getData('Style Name');
                $price = $obj->getData('Original Price');
                $specialPrice = $obj->getData('Discount Price');
                if (!$key || !$price) continue;
                isset($prices[$key]) or $prices[$key] = $price;
                isset($specialPrices[$key]) or $specialPrices[$key] = $specialPrice;
                $prices[$key] = max($prices[$key], $price);
                $specialPrices[$key] = max($specialPrices[$key], $specialPrice);
            }
            foreach ($data as $v) {
                $batchData = $v['batch_data'];
                if (!isset($prices[$batchData['Style Name']])) continue;
                $batchData['max_price'] = $prices[$batchData['Style Name']];
                $batchData['max_special_price'] = $specialPrices[$batchData['Style Name']];
                $connection->update($table, array('batch_data' => serialize($batchData)), array('batch_import_id = ?' => $v['batch_import_id']));
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }
        return $this;
    }
}
