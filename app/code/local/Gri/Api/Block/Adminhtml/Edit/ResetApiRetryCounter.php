<?php

/**
 * @method string getObjectName()
 */
class Gri_Api_Block_Adminhtml_Edit_ResetApiRetryCounter extends Mage_Core_Block_Template
{

    public function addResetButton()
    {
        if ($this->getEditContainerBlock() && $this->getObject() &&
            $this->getObject()->getApiRetryCount() >= $this->getRetryCountLimit()
        ) {
            $this->getEditContainerBlock()->addButton('reset_api_retry_counter', array(
                'label'    => $this->getGriApiHelper()->__('Reset Api Retry Counter'),
                'onclick'  => 'setLocation(\'' . $this->getResetApiCounterUrl() . '\');',
            ), 10);
        }
        return $this;
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Container
     */
    public function getEditContainerBlock()
    {
        return $this->getParentBlock() instanceof Mage_Adminhtml_Block_Widget_Container ?
            $this->getParentBlock() : FALSE;
    }

    /**
     * @return Gri_Api_Helper_Data
     */
    public function getGriApiHelper()
    {
        return Mage::helper('gri_api');
    }

    /**
     * @return Mage_Core_Model_Abstract
     */
    public function getObject()
    {
        if ($this->getObjectName() && $this->getEditContainerBlock()) {
            $getObjectMethod = 'get' . $this->getObjectName();
            if (method_exists($this->getEditContainerBlock(), $getObjectMethod) &&
                $object = $this->getEditContainerBlock()->$getObjectMethod()
            ) return $object;
        }
        return FALSE;
    }

    public function getResetApiCounterUrl()
    {
        if ($this->getObject()) return $this->getUrl('gri_api/resetRetryCounter/reset', array(
            'entity' => base64_encode($this->getObject()->getResourceName()),
            'id' => $this->getObject()->getId(),
        ));
        return '';
    }

    public function getRetryCountLimit()
    {
        return $this->getGriApiHelper()->getRetryCountLimit();
    }
}
