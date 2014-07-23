<?php
class Gri_Cms_Helper_Page extends Mage_Core_Helper_Abstract
{
    public $_brands = array(
        'ninewest',
        'stevemadden',
        'betseyjohnson',
        'eqiq',
        'carolinnaespinosa',
        'jeannepierre',
    );

    public $_shops = array(
        'shoes',
        'bags',
        'clothing',
        'accessories',
    );

    public function getBrand($page)
    {
        $data = explode('/', $page->getData('identifier'));
        if (count($data) > 1 && $value = array_intersect($this->_brands, $data)) {
            $brand = Mage::getModel('catalog/category')->loadByAttribute('url_key', reset($value));
            if ($brand) return $brand;
        }
        return false;
    }

    public function getShop($page)
    {
        $data = explode('/', $page->getData('identifier'));
        if (count($data) > 1 && $value = array_intersect($this->_shops, $data)) {
            $shop = Mage::getModel('catalog/category')->loadByAttribute('url_key', reset($value));
            if ($shop) return $shop;
        }
        return false;
    }
}
