<?php

class Gri_Hamper_Block_Adminhtml_Catalog_Product_Edit_Tab_ExtraGifts extends Mage_Adminhtml_Block_Widget
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('gri/hamper/product/edit/extra_gifts.phtml');
    }

    public function canShowTab()
    {
        return TRUE;
    }

    public function getFieldSuffix()
    {
        return 'product';
    }

    /**
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        if ($this->_getData('product') === NULL) {
            $product = Mage::registry('product');
            $product or $product = FALSE;
            $this->setData('product', $product);
        }
        return $this->_getData('product');
    }

    public function getTabLabel()
    {
        return $this->__('Extra Gifts');
    }

    public function getTabTitle()
    {
        return $this->__('Extra Gifts');
    }

    public function isHidden()
    {
        return FALSE;
    }

    public function isReadonly()
    {
        return $this->getProduct()->getCompositeReadonly();
    }
}
