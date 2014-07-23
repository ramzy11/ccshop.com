<?php

class Gri_Sales_Helper_Creditmemo extends Mage_Core_Helper_Abstract
{

    /**
     * @param Gri_Sales_Model_Order_Creditmemo $creditmemo
     * @return boolean
     */
    public function canAddTransaction($creditmemo)
    {
        $enabled = FALSE;
        if ($creditmemo instanceof Gri_Sales_Model_Order_Creditmemo &&
            in_array($creditmemo->getState(), array($creditmemo::STATE_REFUNDED, $creditmemo::STATE_CLOSED)) &&
            !$creditmemo->getTransactionId() &&
            $creditmemo->getMoneyTotalRefunded()
        ) {
            $enabled = TRUE;
        }
        return $enabled;
    }

    /**
     * @param Mage_Payment_Model_Info $info
     * @return Gri_Sales_Block_Adminhtml_Creditmemo_Transaction_Form
     */
    public function getTransactionForm(Mage_Payment_Model_Info $info)
    {
        /* @var Gri_Sales_Block_Adminhtml_Creditmemo_Transaction_Form $block */
        if (!$block = Mage::app()->getLayout()->getBlock($name = 'creditmemo_transaction_form')) {
            $block = Mage::app()->getLayout()->createBlock('gri_sales/adminhtml_creditmemo_transaction_form', $name, array(
                'payment_info' => $info,
            ));
        }
        return $block;
    }
}
