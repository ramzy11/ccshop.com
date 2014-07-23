<?php
class Magiatec_Colorswatch_Model_System_Config_Source_Position
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'right',
                'label' => Mage::helper('magiatecolorswatch')->__('Right')
            ),
            array(
                'value' => 'left',
                'label' => Mage::helper('magiatecolorswatch')->__('Left')
            ),
            array(
                'value' => 'top',
                'label' => Mage::helper('magiatecolorswatch')->__('Top')
            ),
            array(
                'value' => 'bottom',
                'label' => Mage::helper('magiatecolorswatch')->__('Bottom')
            ),
        );
    }
}
