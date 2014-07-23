<?php

class Gri_Hamper_Block_Adminhtml_Catalog_Product_Edit_Tab_Hamper_Option_Search extends Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Bundle_Option_Search
{

    protected function _prepareLayout()
    {
        $this->setChild(
            'grid',
            $this->getLayout()->createBlock('hamper/adminhtml_catalog_product_edit_tab_hamper_option_search_grid',
                'adminhtml.catalog.product.edit.tab.bundle.option.search.grid')
        );
        return Mage_Adminhtml_Block_Widget::_prepareLayout();
    }

    public function getButtonsHtml()
    {
        if ($this->_getData('buttons_html') === NULL) {
            $addButtonData = array(
                'id' => 'add_button_' . $this->getIndex(),
                'label' => Mage::helper('sales')->__('Add Selected Product(s) to Group'),
                'onclick' => 'bSelection.productGridAddSelected(event)',
                'class' => 'add',
            );
            $this->setData('buttons_html', $this->getLayout()->createBlock('adminhtml/widget_button')->setData($addButtonData)->toHtml());
        }
        return $this->_getData('buttons_html');
    }
}
