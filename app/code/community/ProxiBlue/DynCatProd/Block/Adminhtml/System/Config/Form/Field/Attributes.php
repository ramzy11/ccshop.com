<?php

/**
 * Adminhtml system config array field renderer
 *
 * @category   ProxiBlue
 * @package    DynCatProd
 * @author     Lucas van Staden (sales@proxiblue.com.au)
 */
class ProxiBlue_DynCatProd_Block_Adminhtml_System_Config_Form_Field_Attributes extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract {

    protected $_formatted = array();
    
    public function __construct() {
        
        $layout = Mage::getModel('core/layout');
        $renderer =  $layout->createBlock('dyncatprod/adminhtml_system_config_form_renderer_select'
                                        ,'dyncatprod_dminhtml_system_config_form_renderer_select'
                                        ,array('values'=>Mage::helper('dyncatprod')->attributesOptionArray())
                                        )->setWidth(290);
        $rendererLink =  $layout->createBlock('dyncatprod/adminhtml_system_config_form_renderer_select'
                                        ,'dyncatprod_dminhtml_system_config_form_renderer_select'
                                        ,array('values'=>array('OR'=>'OR','AND'=>'AND')))->setWidth(58);
        
        
        $this->addColumn('link', array(
            'label' => Mage::helper('adminhtml')->__('Link'),
            'renderer' => $rendererLink
        ));
        
        $this->addColumn('attribute', array(
            'label' => Mage::helper('adminhtml')->__('Dynamic Display Attrbutes'),
            'renderer' => $renderer
        ));
        
        $this->addColumn('value', array(
            'label' => Mage::helper('adminhtml')->__('Value'),
        ));

        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('adminhtml')->__('Add Attribute');
        
        parent::__construct();
        $this->setTemplate('dyncatprod/attributes.phtml');
        $this->setHtmlId('_cat_'.Mage::registry('category')->getId());
        
    }

}