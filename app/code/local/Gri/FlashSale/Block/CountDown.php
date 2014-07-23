<?php

/**
 * @method Gri_FlashSale_Model_FlashSale getFlashSale() Get Flash Sale Instance
 * @method Gri_FlashSale_Block_CountDown setFlashSale(Gri_FlashSale_Model_FlashSale $flashSale) Set Flash Sale Instance
 */
class Gri_FlashSale_Block_CountDown extends Mage_Core_Block_Template
{
    const CONFIG_DATE_FORMAT = '%02s %02s, %02s %02s:%02s:%02s';
    public function getStartTime()
    {
        $now = Mage::app()->getLocale()->date();
        $end = Mage::app()->getLocale()->date($this->helper('gri_flashsale')->getActiveFlashSale()->getEnd(), Varien_Date::DATETIME_INTERNAL_FORMAT);
        $countDown = $end->sub($now)->toValue();
        return sprintf('%02s:%02s:%02s', floor($countDown % 360000 / 3600), floor($countDown % 3600 / 60), $countDown % 60);
    }

    public function getNewStartTime()
    {
        $end = Mage::app()->getLocale()->date($this->helper('gri_flashsale')->getActiveFlashSale()->getEnd(), Varien_Date::DATETIME_INTERNAL_FORMAT);
        $end = $end->toArray();

        return sprintf(self::CONFIG_DATE_FORMAT, $this->_renderMonth($end['month']), $end['day'], $end['year'], $end['hour'], $end['minute'], $end['second']) ;
    }

    protected function _renderMonth($monthIndex)
    {
        $months = array ( 1 => 'January',
                          2 => 'February',
                          3 => 'March',
                          4 => 'April',
                          5 => 'May',
                          6 => 'June',
                          7 => 'July',
                          8 => 'August',
                          9 => 'September',
                          10 => 'October',
                          11 => 'November',
                          12 => 'December');

        return isset($months[$monthIndex]) ? $months[$monthIndex] : '';
    }
}
