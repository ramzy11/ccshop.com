<?php

class Gri_Reports_Block_Adminhtml_Sales_Grid_Column_Renderer_Currency extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Currency
{

    public function render(Varien_Object $row)
    {
        $data = $row->getData($key = $this->getColumn()->getIndex());
        if ($data !== NULL && $data !== FALSE && !$data) {
            $row->setData($key, '0.00');
        }
        return parent::render($row);
    }
}
