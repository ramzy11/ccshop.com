<?php

class Gri_Hamper_Block_Adminhtml_Catalog_Product_Edit_Tab_Hamper_Option_Selection extends Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Bundle_Option_Selection
{

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('gri/hamper/product/edit/hamper/option/selection.phtml');
    }

    public function getSelectionSearchUrl()
    {
        return $this->getUrl('gri_hamper/selection/search');
    }
}
