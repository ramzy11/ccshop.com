<?php

class Gri_Newcomer_Helper_Data extends Mage_Core_Helper_Abstract
{
    const CONFIG_PATH_POPUP_BLOCK = 'newcomer/popup/block_id';
    const CONFIG_PATH_POPUP_ENABLED = 'newcomer/popup/enabled';
    const CONFIG_PATH_POPUP_FROM = 'newcomer/popup/from';
    const CONFIG_PATH_POPUP_TO = 'newcomer/popup/to';
    const COOKIE_PATH_POPUP_POPPED = 'np_popped';

    public function isEnabled()
    {
        if (Mage::app()->getCookie()->get(self::COOKIE_PATH_POPUP_POPPED)) return FALSE;
        return Mage::getStoreConfig(self::CONFIG_PATH_POPUP_ENABLED) &&
            Mage::app()->getLocale()->isStoreDateInInterval(
                NULL,
                Mage::getStoreConfig(self::CONFIG_PATH_POPUP_FROM),
                Mage::getStoreConfig(self::CONFIG_PATH_POPUP_TO)
            );
    }

    public function getCookieName()
    {
        return self::COOKIE_PATH_POPUP_POPPED;
    }
}
