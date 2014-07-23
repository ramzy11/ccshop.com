<?php

class Gri_FlashSale_Block_Adminhtml_FlashSale_Edit_Form extends Mage_Adminhtml_Block_System_Config_Form
{

    public function __construct()
    {
        parent::__construct();

        $this->setId('flashSaleForm');
        $this->setTitle(Mage::helper('gri_flashsale')->__('Flash Sale Information'));
    }

    /**
     *
     * @return Mage_Adminhtml_Block_Adminhtml_Flashsale_Edit_Form
     */
    protected function _prepareForm()
    {
        /* @var $helper Gri_FlashSale_Helper_Data */
        $helper = Mage::helper('gri_flashsale');
        /* @var $model Gri_FlashSale_Model_FlashSale */
        $model = Mage::registry('flash_sale');
        /* @see Mage_Adminhtml_Block_System_Config_Form_Field_Image::_getUrl() */
        $imageConfig = new Varien_Simplexml_Element('<config><base_url type="media">flash_sale</base_url></config>');
        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getData('action'),
            'method' => 'post',
            'enctype'   => 'multipart/form-data',
        ));

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend' => $helper->__('Flash Sale Information'),
            'class' => 'fieldset-wide',
        ));
        $this->_addElementTypes($fieldset);

        if ($model->getId()) $fieldset->addField('flash_sale_id', 'hidden', array(
            'name' => 'id',
        ));

        if ($model->getId()) $fieldset->addField('back', 'hidden', array(
            'name' => 'back',
        ));

        $fieldset->addField('title', 'text', array(
            'name' => 'title',
            'label' => $helper->__('Title'),
            'title' => $helper->__('Title'),
            'required' => TRUE,
        ));

        $fieldset->addField('is_active', 'select', array(
            'label' => $helper->__('Active'),
            'title' => $helper->__('Active'),
            'name' => 'is_active',
            'required' => TRUE,
            'options' => Mage::getSingleton('adminhtml/system_config_source_yesno')->toArray(),
        ));

        $model->getStart() and $model->setStart(Mage::app()->getLocale()->date($model->getStart(), Varien_Date::DATETIME_INTERNAL_FORMAT));
        $outputFormat = Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);
        $fieldset->addField('start', 'date', array(
            'name' => 'start',
            'time' => TRUE,
            'style' => 'width:150px!important;',
            'format' => $outputFormat,
            'label' => $helper->__('Flash Sale Start'),
            'title' => $helper->__('Flash Sale Start'),
            'image' => $this->getSkinUrl('images/grid-cal.gif'),
            'required' => TRUE,
        ));

        $model->getEnd() and $model->setEnd(Mage::app()->getLocale()->date($model->getEnd(), Varien_Date::DATETIME_INTERNAL_FORMAT));
        $fieldset->addField('end', 'date', array(
            'name' => 'end',
            'time' => TRUE,
            'style' => 'width:150px!important;',
            'format' => $outputFormat,
            'label' => $helper->__('Flash Sale End'),
            'title' => $helper->__('Flash Sale End'),
            'image' => $this->getSkinUrl('images/grid-cal.gif'),
            'required' => TRUE,
        ));


        $fieldset->addField('image', 'image', array(
            'name' => 'image',
            'label' => $helper->__('Main Banner'),
            'title' => $helper->__('Main Banner (Width 717px)'),
            'field_config' => $imageConfig,
            'required' => TRUE,
        ));
        $fieldset->addField('small_image', 'image', array(
            'name' => 'small_image',
            'label' => $helper->__('Home Page Banner'),
            'title' => $helper->__('Home Page Banner'),
            'field_config' => $imageConfig,
            'required' => TRUE,
        ));
        $fieldset->addField('mobile_image', 'image', array(
            'name' => 'mobile_image',
            'label' => $helper->__('Mobile Main Banner'),
            'title' => $helper->__('Mobile Main Banner'),
            'field_config' => $imageConfig,
            'required' => TRUE,
        ));
        $fieldset->addField('mobile_small_image', 'image', array(
            'name' => 'mobile_small_image',
            'label' => $helper->__('Mobile Home Page Banner'),
            'title' => $helper->__('Mobile Home Page Banner'),
            'field_config' => $imageConfig,
            'required' => TRUE,
        ));

        // 中文
        $fieldset->addField('image_cht', 'image', array(
            'name' => 'image_cht',
            'label' => $helper->__('[中文]Main Banner'),
            'title' => $helper->__('[中文]Main Banner (Width 717px)'),
            'field_config' => $imageConfig,
            'required' => TRUE,
        ));
        $fieldset->addField('small_image_cht', 'image', array(
            'name' => 'small_image_cht',
            'label' => $helper->__('[中文]Home Page Banner'),
            'title' => $helper->__('[中文]Home Page Banner'),
            'field_config' => $imageConfig,
            'required' => TRUE,
        ));
        $fieldset->addField('mobile_image_cht', 'image', array(
            'name' => 'mobile_image_cht',
            'label' => $helper->__('[中文]Mobile Main Banner'),
            'title' => $helper->__('[中文]Mobile Main Banner'),
            'field_config' => $imageConfig,
            'required' => TRUE,
        ));
        $fieldset->addField('mobile_small_image_cht', 'image', array(
            'name' => 'mobile_small_image_cht',
            'label' => $helper->__('[中文]Mobile Home Page Banner'),
            'title' => $helper->__('[中文]Mobile Home Page Banner'),
            'field_config' => $imageConfig,
            'required' => TRUE,
        ));

        $fieldset->addField('definition', 'textarea', array(
            'name' => 'definition',
            'label' => $helper->__('Definition'),
            'title' => $helper->__('Definition'),
            'style' => 'height:24em;',
            'after_element_html' => $helper->__('Example:<br/>Style Name,Quantity,Price<br/>Style Name,Color Code,Quantity,Price<br/>Style Name,Color Code,Size,Quantity,Price'),
            'required' => TRUE,
        ));

        $form->setValues($model->getData());
        $form->setUseContainer(TRUE);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
