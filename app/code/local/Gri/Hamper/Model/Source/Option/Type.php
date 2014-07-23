<?php

class Gri_Hamper_Model_Source_Option_Type
{
    const HAMPER_OPTIONS_TYPES_PATH = 'global/catalog/product/options/hamper/types';

    public function toOptionArray()
    {
        $types = array();

        foreach (Mage::getConfig()->getNode(self::HAMPER_OPTIONS_TYPES_PATH)->children() as $type) {
            $labelPath = self::HAMPER_OPTIONS_TYPES_PATH . '/' . $type->getName() . '/label';
            $types[] = array(
                'label' => (string) Mage::getConfig()->getNode($labelPath),
                'value' => $type->getName()
            );
        }

        return $types;
    }
}
