<?php 
class Magiatec_Colorswatch_Block_Adminhtml_Catalog_Product_Edit_Tab_Productswatch
    extends Mage_Adminhtml_Block_Widget 
        implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected $_productAttributes;
    
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('magiatec/colorswatch/catalog/product/edit/tab/productswatch.phtml');
    }
    
    public function getTabLabel()
    {
        return $this->helper('magiatecolorswatch')->__('Product Swatches');
    }

    public function getTabTitle()
    {
        return $this->helper('magiatecolorswatch')->__('Product Swatches');
    }

    public function canShowTab()
    {
        $_product = Mage::registry('current_product');
        if ($_product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
            return true;
        }
        return false;
    }

    public function isHidden()
    {
        return ($this->getRequest()->getParam('store') != 0) ? true : false;
    }
    
    public function getProductAttributes()
    {
        if (is_null($this->_productAttributes)) {
            $_product = Mage::registry('current_product');
            $this->_productAttributes = $_product->getTypeInstance()
                ->getConfigurableAttributes($_product);
        }
        return $this->_productAttributes;
    }
    
    public function checkAttributes($attrId = null)
    {
        $attributeIds = Mage::getStoreConfig('magiatecolorswatch/settings/attributes');
        $attributeIds = explode(',', $attributeIds);
        
        if ($attrId) {
            return in_array($attrId, $attributeIds);
        }
        
        foreach ($this->getProductAttributes() as $attr) {
            if (in_array($attr->getAttributeId(), $attributeIds)) {
                return true;
            }
        }
        return false;
    }
}
