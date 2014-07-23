<?php

class Gri_Hamper_Block_Adminhtml_Catalog_Product_Edit_Tab_Hamper extends Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Bundle
{

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('gri/hamper/product/edit/hamper.phtml');
    }

    /**
     * @return Gri_Hamper_Helper_Data
     */
    protected function _getHelper()
    {
        return $this->helper('hamper');
    }

    public function getTabUrl()
    {
        return $this->getUrl('gri_hamper/product_edit/form', array('_current' => TRUE));
    }

    /**
     * Prepare layout
     *
     * @return Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Bundle
     */
    protected function _prepareLayout()
    {
        /* @var $button Mage_Adminhtml_Block_Widget_Button */
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')->setData(array(
            'label' => $this->__('Add New Group'),
            'class' => 'add',
            'id' => 'add_new_option',
            'on_click' => 'bOption.add()'
        ));
        $this->setChild('add_button', $button);

        $this->setChild('options_box', $this->getLayout()->createBlock('hamper/adminhtml_catalog_product_edit_tab_hamper_option',
            'adminhtml.catalog.product.edit.tab.bundle.option'));

        return Mage_Adminhtml_Block_Widget::_prepareLayout();
    }

    public function getTabLabel()
    {
        return $this->_getHelper()->__('Hamper Items');
    }

    public function getTabTitle()
    {
        return $this->_getHelper()->__('Hamper Items');
    }
}
