<?php
class Magiatec_Colorswatch_Block_Adminhtml_Catalog_Product_Attribute_Edit_Tab_Attributeswatch
    extends Mage_Adminhtml_Block_Widget 
        implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('magiatec/colorswatch/catalog/product/attribute/edit/tab/attributeswatch.phtml');
    }
    
    public function getTabLabel()
    {
        return $this->helper('magiatecolorswatch')->__('Attribute Swatches');
    }

    public function getTabTitle()
    {
        return $this->helper('magiatecolorswatch')->__('Attribute Swatches');
    }

    public function canShowTab()
    {
        $_attribute = Mage::registry('entity_attribute');
        
        if ($_attribute->getId() AND $_attribute->getIsGlobal() 
                AND $_attribute->getFrontendInput() == 'select' 
                AND $_attribute->getIsConfigurable()) {
            return true;
        }
        return false;
    }

    public function isHidden()
    {
        $_attribute = Mage::registry('entity_attribute');
        $attributeIds = Mage::getStoreConfig('magiatecolorswatch/settings/attributes');
        $attributeIds = explode(',', $attributeIds);
        return !in_array($_attribute->getId(), $attributeIds);
    }
    
    public function getOptions()
    {
        $_attribute = Mage::registry('entity_attribute');
        
        $optionCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
            ->setAttributeFilter($_attribute->getId())
            ->setPositionOrder(Varien_Data_Collection::SORT_ORDER_ASC, true)
            ->load();
        
        return $optionCollection;
    }
}
