<?php

class Gri_Core_Helper_Data extends Mage_Core_Helper_Abstract
{

    public static function microTimeDiff($start, $end = NULL)
    {
        $start = explode(' ', $start);
        $end = explode(' ', $end ? $end : microtime());
        return $end[0] - $start[0] + ($end[1] - $start[1]);
    }
}
