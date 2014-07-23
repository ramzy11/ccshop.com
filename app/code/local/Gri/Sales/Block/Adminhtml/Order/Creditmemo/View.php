<?php

/**
 * @method Gri_Sales_Model_Order_Creditmemo getCreditmemo() Get Current Creditmemo
 */
class Gri_Sales_Block_Adminhtml_Order_Creditmemo_View extends Mage_Adminhtml_Block_Sales_Order_Creditmemo_View
{

    public function __construct()
    {
        parent::__construct();
        if ($this->getCreditmemo()->canCancel()) {
            $this->_addButton('cancel', array(
                    'label'     => Mage::helper('sales')->__('Cancel'),
                    'class'     => 'delete',
                    'onclick'   => 'confirmSetLocation(\''
                        . $this->getGriHelper()->__('Are you sure you want to cancel the Creditmemo?')
                        . '\', \'' . $this->getCancelUrl() . '\')',
                ), 0, 10
            );
        }

        if ($this->getCreditmemo()->canUpdate()) {
            $this->_addButton('edit', array(
                    'label'     => $this->getGriHelper()->__('Edit'),
                    'class'     => 'go',
                    'onclick'   => 'setLocation(\''.$this->getEditUrl().'\')'
                ), 0, 15
            );
        }
    }

    public function getCancelUrl()
    {
        return $this->getUrl('gri_sales/order_creditmemo/cancel', array('creditmemo_id'=>$this->getCreditmemo()->getId()));
    }

    public function getEditUrl()
    {
        return $this->getUrl('gri_sales/order_creditmemo/edit', array('creditmemo_id' => $this->getCreditmemo()->getId()));
    }

    /**
     * @return Gri_Sales_Helper_Data
     */
    public function getGriHelper()
    {
        return Mage::helper('gri_sales');
    }

    public function getRefundUrl()
    {
        return $this->getUrl('gri_sales/order_creditmemo/refund', array('creditmemo_id' => $this->getCreditmemo()->getId()));
    }

    /**
     * @return Mage_Sales_Helper_Data
     */
    public function getSalesHelper()
    {
        return Mage::helper('sales');
    }

    public function getUpdateUrl()
    {
        return $this->getUrl('gri_sales/order_creditmemo/update', array('creditmemo_id' => $this->getCreditmemo()->getId()));
    }
}
