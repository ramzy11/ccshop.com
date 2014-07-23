<?php

class Gri_SalesRule_Block_Adminhtml_Edit_Tab_Conditions extends Mage_Adminhtml_Block_Promo_Quote_Edit_Tab_Conditions
{

    protected function _prepareForm()
    {
        parent::_prepareForm();
        $renderer = $this->getForm()->getElement('conditions_fieldset')->getRenderer();
        $renderer->setOldTemplate($renderer->getTemplate());
        $renderer->setTemplate($template = 'gri/salesrule/promo/fieldset.phtml')->setNewTemplate($template);
        return $this;
    }
}
