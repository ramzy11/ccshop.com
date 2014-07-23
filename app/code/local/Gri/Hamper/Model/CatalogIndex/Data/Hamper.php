<?php

class Gri_Hamper_Model_CatalogIndex_Data_Hamper extends Mage_Bundle_Model_CatalogIndex_Data_Bundle
{

    /**
     * Retreive product type code
     *
     * @return string
     */
    public function getTypeCode()
    {
        return Gri_Hamper_Model_Product_Type::TYPE_HAMPER;
    }
}
