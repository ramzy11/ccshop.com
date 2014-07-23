<?php
if (version_compare(Mage::getVersion(), '1.6.0', '>')) {
    abstract class Magiatec_Colorswatch_Model_Resource_Product_Abstract
        extends Mage_Core_Model_Resource_Db_Abstract {};
}
else {
    abstract class Magiatec_Colorswatch_Model_Resource_Product_Abstract
        extends Mage_Core_Model_Mysql4_Abstract {};
}

class Magiatec_Colorswatch_Model_Resource_Product 
    extends Magiatec_Colorswatch_Model_Resource_Product_Abstract
{
    protected function _construct()
    {
        $this->_init('magiatecolorswatch/product', 'image_id');
    }
}
