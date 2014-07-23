<?php
class Gri_ImportData_Block_Adminhtml_System_Convert_Profile_Edit_Tab_Updateimage extends Mage_Adminhtml_Block_Template
{

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('system/convert/profile/updateimage.phtml');
    }

    public function canUpdate()
    {
        return is_file(Mage::getBaseDir('var') . DS . 'import' . DS . 'productImages.zip');
    }

    public function getRunButtonHtml()
    {
        $html = $this->getLayout()->createBlock('adminhtml/widget_button')->setType('button')
            ->setClass('save')->setLabel($this->__('Update Image in Popup'))
            ->setOnClick('updateImage(true)')
            ->toHtml();

        return $html;
    }
}
