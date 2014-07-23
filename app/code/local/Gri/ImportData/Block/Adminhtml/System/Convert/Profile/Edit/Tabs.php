<?php
class Gri_ImportData_Block_Adminhtml_System_Convert_Profile_Edit_Tabs extends Mage_Adminhtml_Block_System_Convert_Profile_Edit_Tabs
{

    protected function _beforeToHtml()
    {
        $profile = Mage::registry('current_convert_profile');
        $new = !$profile->getId();
        if (!$new) {
            $this->addTab('upload', array(
                'label' => Mage::helper('adminhtml')->__('Upload File'),
                'content' => $this->getLayout()->createBlock('adminhtml/system_convert_gui_edit_tab_upload')
                    ->setTemplate('system/convert/profile/newupload.phtml')->toHtml(),
            ));
            $this->addTabAfter('update-image', array(
                'label' => Mage::helper('adminhtml')->__('Update Image'),
                'content' => $this->getLayout()->createBlock('gri_importdata/adminhtml_system_convert_profile_edit_tab_updateimage')->toHtml(),
            ), 'run');
        }
        parent::_beforeToHtml();
        $this->setActiveTab('upload');
        return $this;
    }
}
