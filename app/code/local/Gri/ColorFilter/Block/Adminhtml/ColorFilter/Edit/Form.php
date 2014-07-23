<?php

class Gri_ColorFilter_Block_Adminhtml_ColorFilter_Edit_Form extends Mage_Adminhtml_Block_System_Config_Form
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('colorFilterForm');
        $this->setTitle(Mage::helper('gri_colorfilter')->__('Color Filter Information'));
    }

    /**
     *
     * @return Mage_Adminhtml_Block_Adminhtml_ColorFilter_Edit_Form
     */
    protected function _prepareForm()
    {
        /* @var $helper Gri_ColorFilter_Helper_Data */
        $helper = Mage::helper('gri_colorfilter');
        /* @var $model Gri_ColorFilter_Model_ColorFilter */
        $model = Mage::registry('color_filter');
        /* @see Mage_Adminhtml_Block_System_Config_Form_Field_Image::_getUrl() */
        $imageConfig = new Varien_Simplexml_Element('<config><base_url type="media">color_filter</base_url></config>');
        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getData('action'),
            'method' => 'post',
            'enctype'   => 'multipart/form-data',
        ));

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend' => $helper->__('Color Filter Information'),
            'class' => 'fieldset-wide',
        ));
        $this->_addElementTypes($fieldset);

        if ($model->getId()) $fieldset->addField('color_id', 'hidden', array(
            'name' => 'id',
        ));

        if ($model->getId()) $fieldset->addField('back', 'hidden', array(
            'name' => 'back',
        ));

        $fieldset->addField('label', 'text', array(
            'name' => 'label',
            'label' => $helper->__('Label'),
            'title' => $helper->__('Label'),
            'required' => TRUE,
        ));

        $fieldset->addField('is_active', 'select', array(
            'label' => $helper->__('Active'),
            'title' => $helper->__('Active'),
            'name' => 'is_active',
            'required' => TRUE,
            'options' => Mage::getSingleton('adminhtml/system_config_source_yesno')->toArray(),
        ));


        $fieldset->addField('image', 'image', array(
            'name' => 'image',
            'label' => $helper->__('Color Image'),
            'title' => $helper->__('Color Image'),
            'field_config' => $imageConfig,
            'required' => TRUE,
        ));

        $form->setValues($model->getData());
        $form->setUseContainer(TRUE);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
