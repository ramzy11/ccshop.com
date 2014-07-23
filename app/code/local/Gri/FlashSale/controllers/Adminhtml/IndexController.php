<?php

class Gri_FlashSale_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action
{
    /**
     * @var Gri_FlashSale_Model_FlashSale
     */
    protected $_flashSale;

    protected function _initAction($title)
    {
        $this->_title($this->__('Flash Sale'))
            ->_title($this->__($title));
        $this->loadLayout()
            ->_setActiveMenu('catalogcustom/flashsale');
        return $this;
    }

    /**
     * @return Gri_FlashSale_Model_FlashSale
     */
    protected function _initFlashSale()
    {
        if (!$this->_flashSale) {
            $this->_flashSale = Mage::getModel('gri_flashsale/flashSale');
            ($id = $this->getRequest()->getParam('id')) and $this->_flashSale->load($id);
            Mage::register('flash_sale', $this->_flashSale);
        }
        return $this->_flashSale;
    }

    public function activateAction()
    {
        $flashSale = $this->_initFlashSale();
        try {
            $flashSale->setIsActive(1)->save();
            $this->_getSession()->addSuccess($this->getHelper()->__('Flash sale was activated successfully.'));
            $this->_redirectSuccess($this->getUrl('*/*/index'));
        } catch (Exception $e) {
            $this->_getSession()->addError($this->getHelper()->__('Unable to activate flash sale.'));
            Mage::logException($e);
            $this->_redirectError($this->getUrl('*/*/index'));
        }
    }

    public function deleteAction()
    {
        $flashSale = $this->_initFlashSale();
        try {
            $flashSale->delete();
            $this->_getSession()->addSuccess($this->getHelper()->__('Flash sale was deleted successfully.'));
            $this->_redirectSuccess($this->getUrl('*/*/index'));
        } catch (Exception $e) {
            $this->_getSession()->addError($this->getHelper()->__('Unable to delete flash sale.'));
            Mage::logException($e);
            $this->_redirectError($this->getUrl('*/*/edit', array('id' => $flashSale->getId())));
        }
    }

    public function editAction()
    {
        $flashSale = $this->_initFlashSale();
        $title = 'Edit Flash Sale';
        $flashSale->getId() or $title = 'New Flash Sale';
        $this->_initAction($title);
        $this->getLayout()->getBlock('content')->append(
            $this->getLayout()->createBlock('gri_flashsale/adminhtml_flashSale_edit', 'adminhtml_flashSale.edit')
        );
        $this->renderLayout();
    }

    /**
     * @return Gri_FlashSale_Helper_Data
     */
    public function getHelper()
    {
        return Mage::helper('gri_flashsale');
    }

    public function gridAction()
    {
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('gri_flashsale/adminhtml_flashSale_grid', 'adminhtml_flashSale.grid')
                ->toHtml()
        );
    }

    public function indexAction()
    {
        $this->_initAction('Manage Flash Sale');
        $this->getLayout()->getBlock('content')->append(
            $this->getLayout()->createBlock('gri_flashsale/adminhtml_flashSale', 'adminhtml_flashSale')
        );
        $this->renderLayout();
    }

    public function massDeleteAction()
    {
        $ids = $this->getRequest()->getParam($this->getRequest()->getParam('massaction_prepare_key'));
        /* @var $flashSale Gri_FlashSale_Model_FlashSale */
        $flashSale = Mage::getModel('gri_flashsale/flashSale');
        if (is_array($ids)) try {
            $count = 0;
            foreach ($ids as $id) {
                $flashSale->unsetData()->load($id)->getId() and $flashSale->delete() && ++$count;
            }
            $this->_getSession()->addSuccess($this->getHelper()->__('Deleted %s flash sale.', $count));
            $this->_redirectSuccess($this->getUrl('*/*/index'));
        }
        catch (Exception $e) {
            Mage::logException($e);
            $this->_getSession()->addError($this->getHelper()->__('Deleted %s flash sale, failed on id %s.', $count, $id));
            $this->_redirectError($this->getUrl('*/*/index'));
        }
        else $this->_redirectUrl($this->getUrl('*/*/index'));
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function saveAction()
    {
        $flashSale = $this->_initFlashSale();
        foreach ($this->getRequest()->getParams() as $k => $v) {
            $flashSale->isProtectedField($k) or $flashSale->setData($k, $v);
        }
        try {
            if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
                $uploader = new Varien_File_Uploader('image');
                $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
                $uploader->addValidateCallback('is_image', Mage::helper('catalog/image'), 'validateUploadFile');
                $uploader->setAllowRenameFiles(TRUE);
                $uploader->setFilesDispersion(TRUE);
                $result = $uploader->save($this->getHelper()->getImagePath('image'));
                $flashSale->setImage(basename($result['path']) . $result['file']);
            }
            if (isset($_FILES['small_image']) && $_FILES['small_image']['error'] == UPLOAD_ERR_OK) {
                $uploader = new Varien_File_Uploader('small_image');
                $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
                $uploader->addValidateCallback('is_image', Mage::helper('catalog/image'), 'validateUploadFile');
                $uploader->setAllowRenameFiles(TRUE);
                $uploader->setFilesDispersion(TRUE);
                $result = $uploader->save($this->getHelper()->getImagePath('small_image'));
                $flashSale->setSmallImage(basename($result['path']) . $result['file']);
            }
            if (isset($_FILES['mobile_image']) && $_FILES['mobile_image']['error'] == UPLOAD_ERR_OK) {
                $uploader = new Varien_File_Uploader('mobile_image');
                $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
                $uploader->addValidateCallback('is_image', Mage::helper('catalog/image'), 'validateUploadFile');
                $uploader->setAllowRenameFiles(TRUE);
                $uploader->setFilesDispersion(TRUE);
                $result = $uploader->save($this->getHelper()->getImagePath('mobile_image'));
                $flashSale->setMobileImage(basename($result['path']) . $result['file']);
            }
            if (isset($_FILES['mobile_small_image']) && $_FILES['mobile_small_image']['error'] == UPLOAD_ERR_OK) {
                $uploader = new Varien_File_Uploader('mobile_small_image');
                $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
                $uploader->addValidateCallback('is_image', Mage::helper('catalog/image'), 'validateUploadFile');
                $uploader->setAllowRenameFiles(TRUE);
                $uploader->setFilesDispersion(TRUE);
                $result = $uploader->save($this->getHelper()->getImagePath('mobile_small_image'));
                $flashSale->setMobileSmallImage(basename($result['path']) . $result['file']);
            }

           // cht
            if (isset($_FILES['image_cht']) && $_FILES['image_cht']['error'] == UPLOAD_ERR_OK) {
                $uploader = new Varien_File_Uploader('image_cht');
                $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
                $uploader->addValidateCallback('is_image', Mage::helper('catalog/image'), 'validateUploadFile');
                $uploader->setAllowRenameFiles(TRUE);
                $uploader->setFilesDispersion(TRUE);
                $result = $uploader->save($this->getHelper()->getImagePath('image_cht'));
                $flashSale->setImageCht(basename($result['path']) . $result['file']);
            }
            if (isset($_FILES['small_image_cht']) && $_FILES['small_image_cht']['error'] == UPLOAD_ERR_OK) {
                $uploader = new Varien_File_Uploader('small_image_cht');
                $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
                $uploader->addValidateCallback('is_image', Mage::helper('catalog/image'), 'validateUploadFile');
                $uploader->setAllowRenameFiles(TRUE);
                $uploader->setFilesDispersion(TRUE);
                $result = $uploader->save($this->getHelper()->getImagePath('small_image_cht'));
                $flashSale->setSmallImageCht(basename($result['path']) . $result['file']);
            }
            if (isset($_FILES['mobile_image_cht']) && $_FILES['mobile_image_cht']['error'] == UPLOAD_ERR_OK) {
                $uploader = new Varien_File_Uploader('mobile_image_cht');
                $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
                $uploader->addValidateCallback('is_image', Mage::helper('catalog/image'), 'validateUploadFile');
                $uploader->setAllowRenameFiles(TRUE);
                $uploader->setFilesDispersion(TRUE);
                $result = $uploader->save($this->getHelper()->getImagePath('mobile_image_cht'));
                $flashSale->setMobileImageCht(basename($result['path']) . $result['file']);
            }
            if (isset($_FILES['mobile_small_image_cht']) && $_FILES['mobile_small_image_cht']['error'] == UPLOAD_ERR_OK) {
                $uploader = new Varien_File_Uploader('mobile_small_image_cht');
                $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
                $uploader->addValidateCallback('is_image', Mage::helper('catalog/image'), 'validateUploadFile');
                $uploader->setAllowRenameFiles(TRUE);
                $uploader->setFilesDispersion(TRUE);
                $result = $uploader->save($this->getHelper()->getImagePath('mobile_small_image_cht'));
                $flashSale->setMobileSmallImageCht(basename($result['path']) . $result['file']);
            }


            $datetimeFormat = Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);
            $flashSale->setStart(Mage::app()->getLocale()->date($flashSale->getStart(), $datetimeFormat, NULL, FALSE));
            $flashSale->setStart($flashSale->getStart()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT));
            $flashSale->setStart(Mage::getSingleton('core/date')->gmtDate(NULL, $flashSale->getStart()));
            $flashSale->setEnd(Mage::app()->getLocale()->date($flashSale->getEnd(), $datetimeFormat, NULL, FALSE));
            $flashSale->setEnd($flashSale->getEnd()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT));
            $flashSale->setEnd(Mage::getSingleton('core/date')->gmtDate(NULL, $flashSale->getEnd()));

            $flashSale->save();

            $this->_getSession()->addSuccess($this->getHelper()->__('Flash sale was saved successfully.'));
            $this->_redirectSuccess($this->getRequest()->getParam('back') == 'edit' ? $this->getUrl('*/*/edit', array(
                'id' => $flashSale->getId(),
            )) : $this->getUrl('*/*/index'));
        } catch (Exception $e) {
            $this->_getSession()->addError($this->getHelper()->__('Unable to save flash sale.'));
            Mage::logException($e);
            $this->_redirectError($this->getUrl('*/*/edit', array('id' => $flashSale->getId())));
        }
    }
}
