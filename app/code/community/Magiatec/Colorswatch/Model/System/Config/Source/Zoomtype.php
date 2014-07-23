<?php
class Magiatec_Colorswatch_Model_System_Config_Source_Zoomtype
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'standard',
                'label' => Mage::helper('magiatecolorswatch')->__('Standard')
            ),
            array(
                'value' => 'innerzoom',
                'label' => Mage::helper('magiatecolorswatch')->__('Inner')
            ),
            array(
                'value' => 'reverse',
                'label' => Mage::helper('magiatecolorswatch')->__('Reverse')
            ),
        );
    }
}
