<?php

class Gri_Core_Block_Site_Switcher extends Mage_Core_Block_Template
{

    public function getCacheKeyInfo()
    {
        return array();
    }

    public function getFlagImage($site)
    {
        return $this->getSkinUrl('images/flags/' . $site . '.png');
    }

    public function getShipTo()
    {
        return '';
    }

    public function getSites()
    {
        $sites = array();
        return $sites;
    }
}
