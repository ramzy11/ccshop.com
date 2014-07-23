<?php

class Gri_Sales_Block_Adminhtml_Order_View extends Mage_Adminhtml_Block_Sales_Order_View
{
    protected $_adminActionsResource = array(
        'core' => 'sales/order/actions/',
        'gri' => 'gri_sales/order/actions/',
    );

    public function __construct()
    {
        parent::__construct();
        $order = $this->getOrder();

        // BDT Cancel
        if ($this->_isAllowedAction('cancel', 'gri') && $order->getState() == 'processing') {
            $message = $this->getGriHelper()->__('Are you sure you want to cancel and refund this order?');
            $this->_addButton('order_cancel', array(
                'label'     => $this->getGriHelper()->__('Cancel and Refund'),
                'onclick'   => 'deleteConfirm(\''.$message.'\', \'' . $this->getGriCancelUrl() . '\')',
                'class'     => 'go',
            ), 0, 30);
        }
    }

    protected function _isAllowedAction($action, $resource = 'core')
    {
        return Mage::getSingleton('admin/session')->isAllowed($this->_adminActionsResource[$resource] . $action);
    }

    public function getGriCancelUrl()
    {
        return $this->getUrl('gri_sales/order/cancel');
    }

    /**
     * @return Gri_Sales_Helper_Data
     */
    public function getGriHelper()
    {
        return Mage::helper('gri_sales');
    }

    /**
     * @return Mage_Sales_Helper_Data
     */
    public function getSalesHelper()
    {
        return Mage::helper('sales');
    }
}
