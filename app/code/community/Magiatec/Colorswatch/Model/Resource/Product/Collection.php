<?php
if (version_compare(Mage::getVersion(), '1.6.0', '>')) {
    abstract class Magiatec_Colorswatch_Model_Resource_Product_Collection_Abstract
        extends Mage_Core_Model_Resource_Db_Collection_Abstract {};
}
else {
    abstract class Magiatec_Colorswatch_Model_Resource_Product_Collection_Abstract
        extends Mage_Core_Model_Mysql4_Collection_Abstract {};
}

class Magiatec_Colorswatch_Model_Resource_Product_Collection 
    extends Magiatec_Colorswatch_Model_Resource_Product_Collection_Abstract
{
    public function _construct()
    {
        $this->_init('magiatecolorswatch/product');
    }
    
    protected function _afterLoad()
    {
        parent::_afterLoad();
        return $this;
    }
}
