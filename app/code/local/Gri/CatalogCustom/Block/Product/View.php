<?php

class Gri_CatalogCustom_Block_Product_View  extends Mage_Catalog_Block_Product_View
{

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->getLayout()->getBlock('root')
            ->addBodyClass('attribute-set-' . $this->getProduct()->getAttributeSetId());
        $this->getProduct()->getFinalPrice();
        return $this;
    }

    public  function getFacebookApiKey(){
        return Mage::getSingleton('inchoo_facebook/config')->getApiKey();
    }

    public  function  getFacebookSecret(){
        return  Mage::getSingleton('inchoo_facebook/config')->getSecret();
    }

    public  function getWeiboApiKey(){
        return Mage::getSingleton('inchoo_weibo/config')->getApiKey();
    }

    public  function  getWeiboSecret(){
        return  Mage::getSingleton('inchoo_weibo/config')->getSecret();
    }

    public function getProductUrl($product, $additional = array())
    {
        if ($product->getIsFlashSale()) return Mage::getUrl('flashsale/product/view/',array('item' => $product->getSku()));
        if ($product->getUrl() !== NULL) return $product->getUrl();
        if (Mage::registry('disable_sold_out_links') && !$product->isSalable()) return FALSE;

        if (($category = $product->getCategory()) && isset($GLOBALS['specialCategories'][$category->getUrlKey()])) {
            $requestPath = $product->getRequestPath() ? $product->getRequestPath() :
                $product->getUrlKey() . Mage::getStoreConfig(Mage_Catalog_Helper_Product::XML_PATH_PRODUCT_URL_SUFFIX);
            $url = dirname($category->getUrl()) . '/' . $category->getUrlKey() . '/' . $requestPath;
        }
        else $url = parent::getProductUrl($product, $additional);
        return $url;
    }
}
