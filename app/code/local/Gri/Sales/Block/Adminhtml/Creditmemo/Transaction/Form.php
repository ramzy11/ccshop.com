<?php

/**
 * Class Gri_Sales_Block_Adminhtml_Creditmemo_Transaction_Form
 * @method boolean getEnabled()
 * @method Mage_Payment_Model_Info getPaymentInfo()
 * @method Gri_Sales_Block_Adminhtml_Creditmemo_Transaction_Form setEnabled(boolean $enabled)
 */
class Gri_Sales_Block_Adminhtml_Creditmemo_Transaction_Form extends Mage_Core_Block_Template
{
    protected $_template = 'gri/sales/creditmemo/transaction/form.phtml';

    /**
     * @return Gri_Sales_Helper_Creditmemo
     */
    protected function _getCreditmemoHelper()
    {
        return $this->helper('gri_sales/creditmemo');
    }

    protected function _toHtml()
    {
        return $this->_getCreditmemoHelper()->canAddTransaction($this->getCreditmemo()) ? parent::_toHtml() : '';
    }

    /**
     * @return Gri_Sales_Model_Order_Creditmemo
     */
    public function getCreditmemo()
    {
        return Mage::registry('current_creditmemo');
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Button
     */
    public function getSaveButton()
    {
        if ($this->_getData('save_button') === NULL) {
            $onclick = "if (!$('add_txn_id') || $('add_txn_id').value=='') {alert('{$this->__('Please input transaction ID.')}');return;} if (confirm('{$this->__('You WON\\\'T BE ABLE modify the transaction ID after save. Are you sure want to assign this transaction ID to current creditmemo?')}')) submitAndReloadArea($('payment_info_block').parentNode, '" . $this->getSaveUrl() . "')";
            $button = $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' => $this->__('Save'),
                    'class' => 'save',
                    'onclick' => $onclick
                ));
            $this->setData('save_button', $button);
        }
        return $this->_getData('save_button');
    }

    public function getSaveUrl()
    {
        return $this->getUrl('gri_sales/order_creditmemo/addTransaction/', array('creditmemo_id' => $this->getCreditmemo()->getId()));
    }
}
