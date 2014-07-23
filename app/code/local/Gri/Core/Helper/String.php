<?php
class Gri_Core_Helper_String extends Mage_Core_Helper_String
{
    /**
     * @param $string
     * @return array
     */
    public function convertStringToArr($string)
    {
        $string = str_replace("\n\n","\n",str_replace("\r","\n",trim($string)));
        $string = explode("\n",trim($string));

        return  array_filter($string);
    }
}
