<?php

abstract class Gri_Reports_Model_Resource_Report_Abstract extends Mage_Reports_Model_Resource_Report_Abstract
{
    const DATE_FORMAT_DAY = 'yyyy-MM-dd';
    const DATE_FORMAT_MONTH = 'yyyy-MM-01';
    const DATE_FORMAT_YEAR = 'yyyy-01-01';

    protected $_isPkAutoIncrement = FALSE;

    public function getDateString($date, $format, $useTimezone = TRUE)
    {
        return Mage::app()->getLocale()
            ->date($date, Varien_Date::DATETIME_INTERNAL_FORMAT, NULL, $useTimezone)
            ->toString($format);
    }

    public function getDayString($date, $useTimezone = TRUE)
    {
        return $this->getDateString($date, self::DATE_FORMAT_DAY, $useTimezone);
    }

    public function getMonthString($date, $useTimezone = TRUE)
    {
        return $this->getDateString($date, self::DATE_FORMAT_MONTH, $useTimezone);
    }

    public function getYearString($date, $useTimezone = TRUE)
    {
        return $this->getDateString($date, self::DATE_FORMAT_YEAR, $useTimezone);
    }

    public function setIdFieldName($name)
    {
        $this->_idFieldName = $name;
        return $this;
    }
}
