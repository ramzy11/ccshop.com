<?php

class Gri_Reports_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_orderItemReportResource;
    protected $_orderReportResource;

    /**
     * @return Gri_Reports_Model_Resource_Report_Order
     */
    public function getOrderItemReportResource()
    {
        if (!$this->_orderItemReportResource) {
            $this->_orderItemReportResource = Mage::getResourceModel('gri_report/report_order');
        }
        return $this->_orderItemReportResource;
    }

    /**
     * @return Gri_Reports_Model_Resource_Report_Order_Item
     */
    public function getOrderReportResource()
    {
        if (!$this->_orderReportResource) {
            $this->_orderReportResource = Mage::getResourceModel('gri_report/report_order_item');
        }
        return $this->_orderReportResource;
    }
}
