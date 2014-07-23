<?php

class Gri_Hamper_Block_Adminhtml_Sales_Order_View_Items_Renderer extends Mage_Bundle_Block_Adminhtml_Sales_Order_View_Items_Renderer
{

    /**
     * @return Gri_Hamper_Helper_Data
     */
    protected function _getHelper()
    {
        return $this->helper('hamper');
    }

    public function canShowPriceInfo($item)
    {
        return !$item->getParentItem();
    }

    public function getValueHtml($item)
    {
        $result = '';
        $this->_getHelper()->getIsGift($item) and $result .= $this->__('Extra Gift') . '<br/>';
        $result .= $this->escapeHtml($item->getName()) . '<br/>';
        $result .= $this->__('SKU: %s', $item->getSku()) . '<br/>';
        $result .= '<strong>' . $this->__('Qty: %s', $item->getQtyOrdered() * 1) . '</strong><br/>';
        return $result;
    }
}
