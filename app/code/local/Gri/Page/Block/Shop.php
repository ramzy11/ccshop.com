<?php
class Gri_Page_Block_Shop extends Mage_Core_Block_Template
{

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        /* @var $root Mage_Page_Block_Html */
        if ($root = $this->getLayout()->getBlock('root')) {
            $this->getShop() and $root->addBodyClass('shop-' . $this->getShop()->getUrlKey());
        }
        return $this;
    }

    /**
     * get Shop from current page
     */
    public function getShop()
    {
        if ($this->getData('shop') === NULL) {
            if (NULL !== $shop = Mage::registry('shop_category')) return $shop;
            $params = $this->getRequest()->getParams();
            // category case and product case
            if (in_array($this->getRequest()->getRouteName(), array("catalog", "gri_catalogcustom"))) {
                $params = $this->getRequest()->getParams();
                if ($this->getRequest()->getControllerName() == 'category') {
                    if (isset($params['id'])) {
                        $category = Mage::getModel('catalog/category')->load($params['id']);
                        $shop = $category->getShopCategory();
                    }
                }
                if (!$shop && $this->getRequest()->getControllerName() == 'product') {
                    if (isset($params['category'])) {
                        $category = Mage::getModel('catalog/category')->load($params['category']);
                        $shop = $category->getShopCategory();
                    }
                }
            }
            // cms page case
            if (!$shop && in_array($this->getRequest()->getRouteName(), array('cms', 'gri_cms'))) {
                if (isset($params['page_id'])) {
                    $page = Mage::getModel('cms/page')->load($params['page_id']);
                    if ($page) {
                        $shop = Mage::helper('gri_cms/page')->getShop($page);
                    }
                }
            }
            $this->setData('shop', $shop);
            Mage::register('shop_category', $shop);
        }
        return $this->getData('shop');
    }

    public function getImageUrl($image){
        return $image ? Mage::getBaseUrl('media').'catalog/category/'.$image : false;
    }

    public function getImageDir($image){
        return $image ? Mage::getBaseDir('media').DS.'catalog'.DS.'category'.DS .$image : false;
    }
}
