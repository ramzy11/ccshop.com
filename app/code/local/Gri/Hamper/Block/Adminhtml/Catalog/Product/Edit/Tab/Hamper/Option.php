<?php

class Gri_Hamper_Block_Adminhtml_Catalog_Product_Edit_Tab_Hamper_Option extends Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Bundle_Option
{

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('gri/hamper/product/edit/hamper/option.phtml');
    }

    protected function _prepareLayout()
    {
        /* @var $button Mage_Adminhtml_Block_Widget_Button */
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')->setData(array(
            'id' => $this->getFieldId() . '_{{index}}_add_button',
            'label' => $this->__('Add Selection'),
            'on_click' => 'bSelection.showSearch(event)',
            'class' => 'add',
        ));
        $this->setChild('add_selection_button', $button);

        $button = $this->getLayout()->createBlock('adminhtml/widget_button')->setData(array(
            'id' => $this->getFieldId() . '_{{index}}_close_button',
            'label' => $this->__('Close'),
            'on_click' => 'bSelection.closeSearch(event)',
            'class' => 'back no-display',
        ));
        $this->setChild('close_search_button', $button);

        $button = $this->getLayout()->createBlock('adminhtml/widget_button')->setData(array(
            'label' => Mage::helper('catalog')->__('Delete Group'),
            'class' => 'delete delete-product-option',
            'on_click' => 'bOption.remove(event)',
        ));
        $this->setChild('option_delete_button', $button);

        $this->setChild('selection_template',
            $this->getLayout()->createBlock('hamper/adminhtml_catalog_product_edit_tab_hamper_option_selection')
        );

        return Mage_Adminhtml_Block_Widget::_prepareLayout();
    }

    public function getImgUrlPath()
    {
        return Mage::getBaseUrl('media') . '/hamper/';
    }

    public function getTypeSelectHtml()
    {
        if ($this->_getData('type_select_html') === NULL) {
            $select = $this->getLayout()->createBlock('adminhtml/html_select')
                ->setData(array(
                    'id' => $this->getFieldId() . '_{{index}}_type',
                    'class' => 'select select-product-option-type required-option-select',
                    'extra_params' => 'onchange="bOption.changeType(event)"'
                ))
                ->setName($this->getFieldName() . '[{{index}}][type]')
                ->setOptions(Mage::getSingleton('hamper/source_option_type')->toOptionArray());

            $this->setData('type_select_html', $select->getHtml());
        }
        return $this->_getData('type_select_html');
    }
}
