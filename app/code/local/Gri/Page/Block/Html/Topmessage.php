<?php

class Gri_Page_Block_Html_Topmessage extends Mage_Core_Block_Template
{
    const COOKIE_PAGE_TOP_MESSAGE = 'page_top_message';

    public function getCookieName(){
        return self::COOKIE_PAGE_TOP_MESSAGE;
    }

    public function isEnabled()
    {
        if (Mage::app()->getCookie()->get(self::COOKIE_PAGE_TOP_MESSAGE)) return false;
        return true;
    }

    public function getIsHomePage()
    {
        $routeName = Mage::app()->getRequest()->getRouteName();
        $identifier = Mage::getSingleton('cms/page')->getIdentifier();

        return $routeName == 'cms' && $identifier == 'home' ? true : false;
    }
}