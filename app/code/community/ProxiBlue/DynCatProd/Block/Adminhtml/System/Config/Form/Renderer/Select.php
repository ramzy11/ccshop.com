<?php

/**
 * Form select renderer
 *
 * @category   ProxiBlue
 * @package    DynCatProd
 * @author     Lucas van Staden (sales@proxiblue.com.au)
 */

class ProxiBlue_DynCatProd_Block_Adminhtml_System_Config_Form_Renderer_Select extends Mage_Core_Block_Abstract {

    public function __construct($attributes = array()) {
        parent::__construct($attributes);
        $this->_prepareOptions();
    }

    /**
     * Override this method in descendants to produce html
     *
     * @return string
     */
    protected function _toHtml() {
        return $this->getElementHtml();
    }

    public function getElementHtml() {
        $rendered = '<select style="width:'. $this->getwidth().'px" name="' . $this->getInputName() . '">';
        foreach ($this->getValues() as $att => $name) {
            $rendered .= '<option value="' . $att . '">' . $name . '</option>';
        }
        $rendered .= '</select>';
        return $rendered;
    }

    protected function _prepareOptions() {
        $values = $this->getValues();
        if (empty($values)) {
            $options = $this->getOptions();
            if (is_array($options)) {
                $values = array();
                foreach ($options as $value => $label) {
                    $values[] = array('value' => $value, 'label' => $label);
                }
            } elseif (is_string($options)) {
                $values = array(array('value' => $options, 'label' => $options));
            }
            $this->setValues($values);
        }
    }

    public function getHtmlAttributes() {
        return array('title', 'class', 'style', 'onclick', 'onchange', 'disabled', 'readonly', 'tabindex');
    }

    public function addClass($class) {
        $oldClass = $this->getClass();
        $this->setClass($oldClass . ' ' . $class);
        return $this;
    }

    protected function _escape($string) {
        return htmlspecialchars($string, ENT_COMPAT);
    }

}
