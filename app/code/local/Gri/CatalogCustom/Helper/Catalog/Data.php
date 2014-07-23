<?php

class Gri_CatalogCustom_Helper_Catalog_Data extends Mage_Catalog_Helper_Data
{

    public function getBreadcrumbPath()
    {
        if (!$this->_categoryPath) {
            $path = parent::getBreadcrumbPath();

            /* @var $productHelper Gri_CatalogCustom_Helper_Product */
            $productHelper = Mage::helper('gri_catalogcustom/product');
            $product = $this->getProduct();
            /* @var $breadcrumbs Mage_Page_Block_Html_Breadcrumbs */
            if ($product) {
                $product->getFinalPrice();
                if ($productHelper->isGift($product)) {
                    /* @var $giftRedemption Mage_Cms_Model_Page */
                    $giftRedemption = Mage::getModel('cms/page')->setStoreId($product->getStoreId())->load('gift-redemption');
                    if ($giftRedemption->getId()) {
                        $label = $giftRedemption->getContentHeading();
                        $label or $label = $giftRedemption->getTitle();
                        $path = array_reverse($path, TRUE);
                        $path['gift-redemption'] = array(
                            'label' => $label,
                            'title' => $label,
                            'link' => Mage::getUrl($giftRedemption->getIdentifier()),
                        );
                        $path = array_reverse($path, TRUE);
                    }
                } else if ($product->getIsFlashSale()) {
                    $label = Mage::helper('gri_flashsale')->__('Flash Sale');
                  //$path = array_reverse($path, TRUE);
                    $path = array();
                    $path['product'] = array (
                        'label' => $product->getName(),
                        'title' => $product->getName(),
                    );
                    $path['gift-redemption'] = array(
                        'label' => $label,
                        'title' => $label,
                        'link' => Mage::getUrl('flashsale'),
                    );
                    $path = array_reverse($path, TRUE);
                }
            }
            $this->_categoryPath = $path;
        }
        return $this->_categoryPath;
    }
}
