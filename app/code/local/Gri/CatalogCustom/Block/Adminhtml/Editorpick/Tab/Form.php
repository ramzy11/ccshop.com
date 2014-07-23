<?php

class Gri_CatalogCustom_Block_Adminhtml_Editorpick_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{

    /**
     * @return Gri_CatalogCustom_Model_Product
     */
    public function getProduct()
    {
        if (!($this->getData('product') instanceof Mage_Catalog_Model_Product)) {
            $this->setData('product', Mage::registry('product'));
        }
        return $this->getData('product');
    }

    protected function _prepareForm()
    {

        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('editorpick_form', array('legend' => Mage::helper('gri_catalogcustom')->__('Item information')));
        $attribute = $this->getProduct()->getResource()->getAttribute($attributeName = 'editors_pick');

        $fieldset->addField('editor', 'text', array(
            'label' => $attribute->getFrontendLabel(),
            'required' => $required = $attribute->getIsRequired(),
            'class' => $required ? 'required-entry' : '',
            'name' => $attributeName,
            'value' => $this->getProduct()->getData($attributeName),
        ));
        /*
         if ( Mage::getSingleton('adminhtml/session')->getNewsData() )
         {
             $form->setValues(Mage::getSingleton('adminhtml/session')->getNewsData());
             Mage::getSingleton('adminhtml/session')->setNewsData(null);
         } elseif ( Mage::registry('news_data') ) {
             $form->setValues(Mage::registry('news_data')->getData());
         }
        */
        return parent::_prepareForm();
    }
}
