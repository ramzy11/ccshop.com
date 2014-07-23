<?php

class Gri_CatalogCustom_Model_Resource_Entity_Attribute_Option_Collection extends Mage_Eav_Model_Resource_Entity_Attribute_Option_Collection
{

    public function setPositionOrder($dir = self::SORT_ORDER_ASC, $sortAlpha = FALSE)
    {
        parent::setPositionOrder($dir, $sortAlpha);
        $this->setOrder('value', self::SORT_ORDER_ASC);
        return $this;
    }

    public function setStoreFilter($storeId = NULL, $useDefaultValue = TRUE)
    {
        parent::setStoreFilter($storeId, $useDefaultValue);
        $this->unsetOrder('value');
        return $this;
    }

    /**
     * Convert collection items to select options hash array
     *
     * @return array
     */
    public function toOptionHash()
    {
        return parent::_toOptionHash('option_id', 'value');
    }

    public function unsetOrder($key)
    {
        unset($this->_orders[$key]);
        return $this;
    }
}
