<?php

class Gri_Reports_Model_Resource_Report_Order_Item extends Gri_Reports_Model_Resource_Report_Abstract
{

    protected function _construct()
    {
        $this->_init('gri_reports/report_order_item', 'order_item_id');
    }
}
