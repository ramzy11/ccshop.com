<?php

class Gri_Reports_Model_Resource_Report_Coupon extends Gri_Reports_Model_Resource_Report_Abstract
{

    protected function _construct()
    {
        $this->_init('salesrule/coupon', 'coupon_id');
    }
}
