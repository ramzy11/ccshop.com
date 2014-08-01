<?php
/**
 * @method integer getMaxSwatches()
 * @method string getShopNowOnclick()
 * @method boolean getShowDescription()
 * @method boolean getShowMoreSwatches()
 * @method boolean getShowSpConfig()
 * @method boolean getUseShopNow()
 * @method Gri_CatalogCustom_Block_Product_List_Item setProduct(Mage_Catalog_Model_Product $product)
 * @method Gri_CatalogCustom_Block_Product_List_Item setImageHeight(integer $height)
 * @method Gri_CatalogCustom_Block_Product_List_Item setImageWidth(integer $width)
 * @method Gri_CatalogCustom_Block_Product_List_Item setMaxSwatches(integer $count)
 * @method Gri_CatalogCustom_Block_Product_List_Item setShowActions(boolean $show)
 * @method Gri_CatalogCustom_Block_Product_List_Item setShowDescription(boolean $show)
 * @method Gri_CatalogCustom_Block_Product_List_Item setShowMoreSwatches(boolean $show)
 * @method Gri_CatalogCustom_Block_Product_List_Item setShowSpConfig(boolean $show)
 * @method Gri_CatalogCustom_Block_Product_List_Item setShopNowLabel(string $label)
 * @method Gri_CatalogCustom_Block_Product_List_Item setShopNowOnclick(string $onclick)
 * @method Gri_CatalogCustom_Block_Product_List_Item setUseShopNow(boolean $use)
 */
class Gri_CatalogCustom_Block_Product_List_Item extends Mage_Catalog_Block_Product_Abstract
{
    protected $_productListQuickviewBlock;

    protected function _construct()
    {
        parent::_construct();
        $this->getTemplate() or $this->setTemplate('catalog/product/list/item.phtml');
        $this->getCacheLifetime() === NULL and $this->setCacheLifetime(3600 + mt_rand(0, 120));
        $this->getUseAddToCart() === NULL and $this->setUseAddToCart(TRUE);
        $this->getUseShopNow() === NULL and $this->setUseShopNow(TRUE);
        $this->getShowActions() === NULL and $this->setShowActions(TRUE);
        $this->getShowPrice() === NULL and $this->setShowPrice(TRUE);
        $this->getShowRewardPoints() === NULL and $this->setShowRewardPoints(FALSE);
        $this->getShopNowLabel() === NULL and $this->setShopNowLabel('Shop Now');
        $this->getMaxSwatches() === NULL and $this->setMaxSwatches(4);
        $this->getShowQuickview() === NULL and $this->setShowQuickview(TRUE);
    }

    public function getCacheKeyInfo()
    {
        $this->setCacheTags(array('PRODUCT-' . $this->getProduct()->getId()));
        return array_merge(
            parent::getCacheKeyInfo(),
            $this->getLayout()->getUpdate()->getHandles(),
            array(
                'product-' . $this->getProduct()->getId(),
                'currency-' . Mage::app()->getStore()->getCurrentCurrencyCode(),
            )
        );
    }

    public function getDescriptionHtml()
    {
        /* @var $block Gri_CatalogCustom_Block_Product_View_Description */
        if (!$block = $this->getDescriptionBlock()) {
            $block = $this->getLayout()->createBlock('gri_catalogcustom/product_view_description', 'product.item.description', array(
                'template' => 'catalog/product/view/description.phtml',
                'hide_review' => TRUE,
                'disable_tabs_script' => TRUE,
            ));
            $this->setDescriptionBlock($block);
        }
        return $block->setProduct($this->getProduct())->toHtml();
    }

    public function getImageHeight()
    {
        if ($this->getData('image_height')) return $this->getData('image_height');
        /*return $this->getProduct()->getTypeInstance() instanceof Mage_Catalog_Model_Product_Type_Grouped ?
            400 : 180;*/
        return $this->getProduct()->getTypeInstance() instanceof Mage_Catalog_Model_Product_Type_Grouped ?
            400 : 320;
    }

    public function getImageWidth()
    {
        if ($this->getData('image_width')) return $this->getData('image_width');
        /*return $this->getProduct()->getTypeInstance() instanceof Mage_Catalog_Model_Product_Type_Grouped ?
            200 : 180;*/
        return $this->getProduct()->getTypeInstance() instanceof Mage_Catalog_Model_Product_Type_Grouped ?
            200 : 220;
    }

    public function getMediaHtml()
    {
        /* @var $block Mage_Catalog_Block_Product_View_Media */
        if (!$block = $this->getMediaBlock()) {
            $block = $this->getLayout()->createBlock('catalog/product_view_media', 'product.item.media', array(
                'template' => 'magiatec/colorswatch/catalog/product/view/lite_media.phtml',
            ));
            $this->setMediaBlock($block);
        }
        return $block->setProduct($this->getProduct())->toHtml();
    }

    public function getProductUrl($product, $additional = array())
    {
        if ($product->getUrl() !== NULL) return $product->getUrl();
        if (Mage::registry('disable_sold_out_links') && !$product->isSalable()) return FALSE;
        if ($product->getData('product_url')) return Mage::getUrl($product->getData('product_url'), $additional);
        if (($category = $product->getCategory()) && isset($GLOBALS['specialCategories'][$category->getUrlKey()])) {
            $requestPath = $product->getRequestPath() ? $product->getRequestPath() :
                $product->getUrlKey() . Mage::getStoreConfig(Mage_Catalog_Helper_Product::XML_PATH_PRODUCT_URL_SUFFIX);
            $url = dirname($category->getUrl()) . '/' . $category->getUrlKey() . '/' . $requestPath;
        }
        else $url = parent::getProductUrl($product, $additional);
        return $url;
    }

    public function getRewardPointsHtml(Mage_Catalog_Model_Product $product)
    {
        return Mage::helper('gri_catalogcustom/product')->getRewardPointsHtml($product);
    }

    /**
     * @param Mage_Catalog_Model_Product
     * @return string
     */
    public function getShopNowUrl($product)
    {
        if ($product->getShopNowUrl() !== NULL) return $product->getShopNowUrl();
        return $this->getProductUrl($product);
    }

    public function showActions()
    {
        return $this->getShowActions();
    }

    public function showPrice()
    {
        return $this->getShowPrice();
    }

    public function showRewardPoints()
    {
        return $this->getShowRewardPoints();
    }

    public function useAddToCart()
    {
        return $this->getUseAddToCart();
    }

    public function useLogin()
    {
        return $this->getUseLogin();
    }

    public function useShopNow()
    {
        return $this->getUseShopNow();
    }

    public function showSwatch()
    {
        return $this->hasData('show_color_swatch') ? $this->getShowColorSwatch() :true;
    }

    public function useShowQuickview()
    {
        return $this->getShowQuickview();
    }

    public function getQuickviewUrl($product){
        if(!$product){
            return null;
        }
        $productId = $product->getId();
        $routePath = 'catalogcustom/product/quickview';
        $routeParams = array('id' => $productId);
        $quickviewUrl = Mage::getUrl($routePath,$routeParams);
        return $quickviewUrl;
    }

    public function showGiftProduct()
    {
        return $this->hasData('show_gift_product') ? $this->getShowGiftProduct() :true;
    }

}
