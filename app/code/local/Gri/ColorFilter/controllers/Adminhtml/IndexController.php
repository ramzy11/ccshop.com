<?php

class Gri_ColorFilter_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action
{
    /**
     * @var Gri_ColorFilter_Model_ColorFilter
     */
    protected $_colorfilter;

    protected function _initAction($title)
    {
        $this->_title($this->__('Color Filter'))
            ->_title($this->__($title));
        $this->loadLayout()
            ->_setActiveMenu('gri/colorfilter');
        return $this;
    }

    /**
     * @return Gri_ColorFilter_Model_ColorFilter
     */
    protected function _initColorFilter()
    {
        if (!$this->_colorfilter) {
            $this->_colorfilter = Mage::getModel('gri_colorfilter/colorFilter');
            ($id = $this->getRequest()->getParam('id')) and $this->_colorfilter->load($id);
            Mage::register('color_filter', $this->_colorfilter);
        }
        return $this->_colorfilter;
    }

    public function activateAction()
    {
        $colorFilter = $this->_initColorFilter();
        try {
            $colorFilter->setIsActive(1)->save();
            $this->_getSession()->addSuccess($this->getHelper()->__('Color was activated successfully.'));
            $this->_redirectSuccess($this->getUrl('*/*/index'));
        } catch (Exception $e) {
            $this->_getSession()->addError($this->getHelper()->__('Unable to activate color.'));
            Mage::logException($e);
            $this->_redirectError($this->getUrl('*/*/index'));
        }
    }

    public function deleteAction()
    {
        $colorFilter = $this->_initColorFilter();
        try {
            $colorFilter->delete();
            $this->_getSession()->addSuccess($this->getHelper()->__('Color was deleted successfully.'));
            $this->_redirectSuccess($this->getUrl('*/*/index'));
        } catch (Exception $e) {
            $this->_getSession()->addError($this->getHelper()->__('Unable to delete color.'));
            Mage::logException($e);
            $this->_redirectError($this->getUrl('*/*/edit', array('id' => $colorFilter->getId())));
        }
    }

    public function editAction()
    {
        $colorFilter = $this->_initColorFilter();
        $title = 'Edit Color';
        $colorFilter->getId() or $title = 'New Color';
        $this->_initAction($title);
        $this->getLayout()->getBlock('content')->append(
            $this->getLayout()->createBlock('gri_colorfilter/adminhtml_colorFilter_edit', 'adminhtml_colorFilter.edit')
        );
        $this->renderLayout();
    }

    /**
     * @return Gri_ColorFilter_Helper_Data
     */
    public function getHelper()
    {
        return Mage::helper('gri_colorfilter');
    }

    public function gridAction()
    {
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('gri_colorfilter/adminhtml_colorFilter_grid', 'adminhtml_colorFilter.grid')
                ->toHtml()
        );
    }

    public function indexAction()
    {
        $this->_initAction('Manage Color Filter');
        $this->getLayout()->getBlock('content')->append(
            $this->getLayout()->createBlock('gri_colorfilter/adminhtml_colorFilter', 'adminhtml_colorFilter')
        );
        $this->renderLayout();
    }

    public function massDeleteAction()
    {
        $ids = $this->getRequest()->getParam($this->getRequest()->getParam('massaction_prepare_key'));
        /* @var $colorFilter Gri_ColorFilter_Model_ColorFilter */
        $colorFilter = Mage::getModel('gri_colorfilter/colorFilter');
        if (is_array($ids)) try {
            $count = 0;
            foreach ($ids as $id) {
                $colorFilter->unsetData()->load($id)->getId() and $colorFilter->delete() && ++$count;
            }
            $this->_getSession()->addSuccess($this->getHelper()->__('Deleted %s Color.', $count));
            $this->_redirectSuccess($this->getUrl('*/*/index'));
        }
        catch (Exception $e) {
            Mage::logException($e);
            $this->_getSession()->addError($this->getHelper()->__('Deleted %s color, failed on id %s.', $count, $id));
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
        $colorFilter = $this->_initColorFilter();
        foreach ($this->getRequest()->getParams() as $k => $v) {
            //$colorFilter->isProtectedField($k) or $colorFilter->setData($k, $v);
            $colorFilter->setData($k, $v);
        }
        try {
            if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
                $uploader = new Varien_File_Uploader('image');
                $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
                $uploader->addValidateCallback('is_image', Mage::helper('catalog/image'), 'validateUploadFile');
                $uploader->setAllowRenameFiles(TRUE);
                $uploader->setFilesDispersion(TRUE);
                $result = $uploader->save($this->getHelper()->getImagePath('image'));
                $colorFilter->setImage(basename($result['path']) . $result['file']);
            }

            $colorFilter->save();
            $this->_getSession()->addSuccess($this->getHelper()->__('Color was saved successfully.'));
            $this->_redirectSuccess($this->getRequest()->getParam('back') == 'edit' ? $this->getUrl('*/*/edit', array(
                'id' => $colorFilter->getId(),
            )) : $this->getUrl('*/*/index'));
        } catch (Exception $e) {
            $this->_getSession()->addError($this->getHelper()->__('Unable to save color.'));
            Mage::logException($e);
            $this->_redirectError($this->getUrl('*/*/edit', array('id' => $colorFilter->getId())));
        }
    }
}
