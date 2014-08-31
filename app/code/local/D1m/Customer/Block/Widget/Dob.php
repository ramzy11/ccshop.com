<?php

/**
 * Rewrite Dob block
 */
class D1m_Customer_Block_Widget_Dob extends Mage_Customer_Block_Widget_Dob {
    /**
     * Get tags with numbers
     * @param $tags
     * @param $label
     * @param $value
     * @return string
     */
    private function getTaggedNumber($tags, $label, $value) {
        if(!empty($tags)) {
            $selectedStr = $value == $label?' selected="selected"':'';
            return '<'.$tags.$selectedStr.'>'.$label.'</'.$tags.'>';
        }
        else {
            return $label;
        }
    }

    /**
     * Get Month numbers for html
     * @param $tags
     * @param $value
     * @return array
     */
    public function GetMonthNumbers($tags, $value) {
        $result = array();
        for($i=1;$i<13;$i++) {
            $result[$i] = $this->getTaggedNumber($tags, $i, $value);
        }
        return $result;
    }

    /**
     * Get Day numbers for html
     * @param $tags
     * @param $value
     * @return array
     */
    public function GetDayNumbers($tags, $value) {
        $result = array();
        for($i=1;$i<32;$i++) {
            $result[$i] = $this->getTaggedNumber($tags, $i, $value);
        }
        return $result;
    }
    public function getDateFormatStr($format='m-d-Y'){
        return $this->getTime()?date($format,$this->getTime()):'';
    }
}