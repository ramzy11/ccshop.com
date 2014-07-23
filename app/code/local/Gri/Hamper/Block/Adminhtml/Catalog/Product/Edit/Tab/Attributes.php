<?php

class Gri_Hamper_Block_Adminhtml_Catalog_Product_Edit_Tab_Attributes extends Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Attributes
{

    protected function _prepareForm()
    {
        parent::_prepareForm();
        if ($price = $this->getForm()->getElement('price')) {
            /* @var $fieldset Varien_Data_Form_Element_Fieldset */
            $fieldset = $price->getContainer();
            $hamperDiscount = $this->getProduct()->getResource()->getAttribute($code = 'hamper_discount');
            $fieldset->addField($code, 'textarea', array(
                'name' => $code,
                'label' => $hamperDiscount->getFrontendLabel(),
                'entity_attribute' => $hamperDiscount,
                'note' => $this->__('For dynamic price only. Format:<br/>Original Price,Discount<br/>e.g.:<br/>3000,100<br/>4000,85%<br/>That means: original price >= $3000 will get $100 discount, and original price >= $4000 will get 15% off (to 85%)'),
                'value' => $this->getProduct()->getData($code),
            ), 'price');
        }
        return $this;
    }
}
