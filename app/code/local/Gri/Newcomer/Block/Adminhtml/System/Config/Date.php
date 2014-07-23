<?php

class Gri_Newcomer_Block_Adminhtml_System_Config_Date extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $format = (string)$element->getFieldConfig()->format;
        $format or $format = Varien_Date::DATE_INTERNAL_FORMAT;
        $element->setFormat($format);
        $element->setImage(Mage::getDesign()->getSkinUrl('images/grid-cal.gif'));
        return $element->getElementHtml();
    }
}
