<?php

class Gri_Api_Helper_Data extends Mage_Core_Helper_Abstract
{
    const CONFIG_PATH_RETRY_COUNT_LIMIT = 'gri_api/global/retry_count_limit';

    public function addRetryCountFilter(Mage_Core_Model_Resource_Db_Collection_Abstract $collection)
    {
        $collection->addFieldToFilter('api_retry_count', array(
            'lt' => $this->getRetryCountLimit(),
        ));
        return $this;
    }

    public function getRetryCountLimit()
    {
        return Mage::getStoreConfig(self::CONFIG_PATH_RETRY_COUNT_LIMIT);
    }

    public function increaseRetryCount(Mage_Core_Model_Abstract $object, $save = TRUE)
    {
        $object->setApiRetryCount($object->getApiRetryCount() + 1);
        $save and $object->save();
        return $this;
    }

    public function isDebugMode()
    {
        return is_file(Mage::getBaseDir() . DS . 'api_debug_mode');
    }

    public function resetRetryCount(Mage_Core_Model_Abstract $object, $save = TRUE)
    {
        $object->setApiRetryCount(0);
        $save and $object->save();
        return $this;
    }
}
