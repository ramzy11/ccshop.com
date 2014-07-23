<?php

class Gri_Api_Adminhtml_ResetRetryCounterController extends Mage_Adminhtml_Controller_Action
{

    /**
     * @return Gri_Api_Helper_Data
     */
    public function getHelper()
    {
        return Mage::helper('gri_api');
    }

    public function resetAction()
    {
        if (($entity = $this->getRequest()->getParam('entity')) &&
            ($entity = base64_decode($entity)) &&
            ($id = $this->getRequest()->getParam('id')) &&
            ($model = Mage::getModel($entity)) &&
            $model->load($id)->getId()
        ) {
            try {
                $this->getHelper()->resetRetryCount($model);
                $this->_getSession()->addSuccess($this->getHelper()->__('API retry counter was reset successfully.'));
            }
            catch (Exception $e) {
                Mage::logException($e);
                $this->_getSession()->addError($this->getHelper()->__('Unable to reset API retry counter.'));
            }
        }
        else $this->_getSession()->addError($this->getHelper()->__('Invalid entity or id.'));
        $this->_redirectReferer();
    }
}
