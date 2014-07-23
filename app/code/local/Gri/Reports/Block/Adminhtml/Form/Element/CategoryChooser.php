<?php

/**
 * Class Gri_Reports_Block_Adminhtml_Form_Element_CategoryChooser
 * @property Mage_Adminhtml_Block_Widget_Form_Renderer_Fieldset_Element $_renderer
 * @method string getChooserUrl()
 * @method string getJsFormObject()
 */
class Gri_Reports_Block_Adminhtml_Form_Element_CategoryChooser extends Varien_Data_Form_Element_Text
{

    protected function _construct()
    {
        parent::_construct();
        $this->getJsFormObject() === NULL and $this->setJsFormObject('filter_form');
        $this->getChooserUrl() === NULL and $this->setChooserUrl(Mage::getUrl('*/*/categoryChooser', array(
            'form' => $this->getJsFormObject(),
        )));
    }

    public function getHtml()
    {
        if ($this->_renderer instanceof Mage_Adminhtml_Block_Template) {
            $oldTemplate = $this->_renderer->getTemplate();
            $template = $this->_getData('template');
            $template or $template = 'gri/reports/form/element/renderer/category_chooser.phtml';
            $this->_renderer->setTemplate($template);
        }
        $this->addClass('category-chooser element-value-changer');
        $html = parent::getHtml();
        if ($this->_renderer instanceof Mage_Adminhtml_Block_Template) {
            $this->_renderer->setTemplate($oldTemplate);
        }
        return $html;
    }
}
