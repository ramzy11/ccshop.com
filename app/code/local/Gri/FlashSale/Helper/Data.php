<?php

class Gri_FlashSale_Helper_Data extends Mage_Core_Helper_Abstract
{
    CONST CONFIG_PATH_FLASH_SALE_ENABLED = 'flashsale/settings/enabled';

    protected $_removeUnavailableOptions = FALSE;
    protected $_productConfigBlock;

    /**
     * @return Gri_FlashSale_Model_FlashSale
     */
    public function getActiveFlashSale()
    {
        if (!$flashSale = Mage::registry('flash_sale')) {
            if (Mage::getStoreConfig(self::CONFIG_PATH_FLASH_SALE_ENABLED)) {
                /* @var $flashSale Gri_FlashSale_Model_FlashSale */
                $flashSale = Mage::getModel('gri_flashsale/flashSale');
                $flashSale->loadActiveFlashSale();
            }
            else $flashSale = FALSE;
            Mage::register('flash_sale', $flashSale);
        }
        return $flashSale;
    }

    /**
     * @param Gri_CatalogInventory_Model_Stock_Item $item
     * @return float
     */
    public function getSalableQty($item)
    {
        $qty = $item->getData('qty') * 1;
        if (Mage::getDesign()->getArea() != 'adminhtml' &&
            ($flashSale = $this->getActiveFlashSale()) &&
            ($product = $flashSale->getProductById($item->getProductId())) &&
            ($product->getQty() + $product->getColorQty() + $product->getParentQty())
        ) {
            $qtys = array();
            $product->getQty() * 1 and $qtys[] = $product->getQty() - $product->getQtyOrdered();
            $product->getColorQty() * 1 and $qtys[] = $product->getColorQty() - $product->getResource()->getColorQtyOrdered($product);
            $product->getParentQty() * 1 and $qtys[] = $product->getParentQty() - $product->getResource()->getParentQtyOrdered($product);
            $minQty = min($qtys) * 1;
            $minQty > 0 and $qty = min($qty, $minQty);
        }
        return $qty;
    }

    public function getImagePath($filename = '')
    {
        return Mage::getBaseDir('media') . DS . 'flash_sale' . DS . str_replace('/', DS, $filename);
    }

    public function getImageUrl($filename = '', $absolute = TRUE)
    {
        $url = 'flash_sale/' . $filename;
        $absolute and $url = Mage::getBaseUrl('media') . $url;
        return $url;
    }

    /**
     * @return Gri_FlashSale_Block_Product_View_Config
     */
    public function getProductConfigBlock()
    {
        $this->_productConfigBlock or $this->_productConfigBlock = Mage::app()->getLayout()
            ->createBlock('gri_flashsale/product_view_config', '', array('flash_sale_config_json' => ''));
        return $this->_productConfigBlock;
    }

    public function getRemoveUnavailableOptions()
    {
        return $this->_removeUnavailableOptions;
    }

    public function removeUnavailableOptions(Mage_Catalog_Model_Product $product, array &$options)
    {
        if ($this->getRemoveUnavailableOptions()) {
            Mage::register('remove_unavailable_products', TRUE, TRUE);
            $result = $options;
            $this->getProductConfigBlock()
                ->unsetData('flash_sale_config_json')
                ->setProduct($product);
            $flashSaleConfig = Mage::helper('core')->jsonDecode($this->getProductConfigBlock()->getFlashSaleConfigJson());
            foreach ($options as $k => $option) {
                $existence = FALSE;
                foreach ($option['products'] as $productId) if (isset($flashSaleConfig['availableProducts'][$productId])) {
                    $existence = TRUE;
                    break;
                }
                if (!$existence) unset($result[$k]);
            }
            $options = $result;
        }
        return $this;
    }

    public function setRemoveUnavailableOptions($removeUnavailableOptions = TRUE)
    {
        Mage::register('remove_unavailable_products', TRUE, TRUE);
        $this->_removeUnavailableOptions = $removeUnavailableOptions;
    }

    public function updateFinalPrice(Mage_Catalog_Model_Product $product)
    {
        $flashSale = $this->getActiveFlashSale();
        if ($product->getId() && !$product->getFlashSalePriceCalculated() && $flashSale->getId()
            && $flashSaleParentProduct = $flashSale->getParentProductById($product->getId())
        ) {
//            $product->setFlashSalePriceCalculated(TRUE);
            if (($parentQty = 1 * $flashSaleParentProduct->getParentQty()) &&
                $parentQty <=  $flashSaleParentProduct->getResource()->getParentQtyOrdered($flashSaleParentProduct) * 1
            ) return $this;
            $product->setFinalPrice($flashSaleParentProduct->getParentPrice());
            $product->setMinimalPrice($flashSaleParentProduct->getMinimalPrice());
            if ($options = $product->getCustomOptions()) {
                if (isset($options['simple_product'])) {
                    /* @var $simpleProduct Gri_CatalogCustom_Model_Product */
                    if (!$simpleProduct = $options['simple_product']->getProduct()) {
                        $simpleProduct = Mage::getModel('catalog/product')->load($options['simple_product']->getProductId());
                        $options['simple_product']->setProduct($simpleProduct);
                    }
                    $flashSaleProduct = $flashSale->getProductById($simpleProduct->getId());
                    $flashSaleProduct and $product->setFinalPrice($flashSaleProduct->getPrice());
                }
            }
            $product->setIsFlashSale(TRUE);
        }
        return $this;
    }

    public function updateQtyOrdered($items, $operator = '+')
    {
        /* @var $item Mage_Sales_Model_Quote_Item */
        if ($flashSale = $this->getActiveFlashSale()) foreach ($items as $item) {
            $qtyOrdered = $operator == '+' ? 1 * $item->getTotalQty() : -1 * $item->getTotalQty();
            // Flash sale product found
            if ($flashSaleProduct = $flashSale->getProductById($item->getProductId())) {
                // Update quantity ordered
                $flashSaleProduct->getResource()->updateQtyOrdered($flashSaleProduct, $qtyOrdered);
                // Update color quantity ordered
                $flashSaleProduct->getResource()->updateColorQtyOrdered($flashSaleProduct, $qtyOrdered);
                // Update parent quantity ordered
                $flashSaleProduct->getResource()->updateParentQtyOrdered($flashSaleProduct, $qtyOrdered);
            }
        }
        return $this;
    }

    public function updateFlashSaleCategoryProducts()
    {
        /* @var $flashsale Gri_FlashSale_Model_FlashSale */
        $flashSale = Mage::getModel('gri_flashsale/flashSale')->loadActiveFlashSale();

        /* @var $collection Gri_FlashSale_Model_Resource_Product_Collection */
        $collection = $flashSale->getAssociatedProducts();

        $products = array();
        foreach( $collection as $item ) {
            $products[strtolower($item->getAttributeSetName())][] = $item->getId();
        }

        /* @var $adapter Varien_Db_Adapter_Interface */
        $adapter = Mage::getSingleton('core/resource')->getConnection('core_write');
        $flashSaleCategoryIds = $this->getFlashSaleCategoryIds();

       /**
        * Delete products from category
        */
        $condition = array (
            'category_id IN (?)' => array_values($flashSaleCategoryIds),
        );
        $adapter->delete('catalog_category_product', $condition);

        try{
            $categoryUnderFlashSaleProducts = $categoryInFlashSaleProducts  = array();
            foreach($products as $urlKey => $_ids){
                foreach($_ids as $pid) {
                    if(isset($flashSaleCategoryIds[$urlKey])) {
                        $categoryUnderFlashSaleProducts[] = array('category_id'=> $flashSaleCategoryIds[$urlKey], 'product_id'=> $pid, 'position'=> 1);
                        $categoryInFlashSaleProducts[] = array('category_id'=> $this->_getFlashSaleCategory()->getId(), 'product_id'=> $pid, 'position'=> 1);
                    }
                }
            }

            if(count($products)){
                /* Create products in flashSale categories(Included) */
                $adapter->insertMultiple('catalog_category_product', $categoryUnderFlashSaleProducts);
                $adapter->insertMultiple('catalog_category_product', $categoryInFlashSaleProducts);
            }

            /* @var $indexProcessPrice Mage_Index_Model_Process */
            $indexProcessPrice = Mage::getModel('index/process')->load('catalog_category_product', 'indexer_code');
            $indexProcessPrice->reindexEverything();
        }catch (Exception $e){
            Mage::logException($e);
        }

        return $this;
    }

    /**
     * @return array
     */
    protected function getFlashSaleCategoryIds()
    {
        $cIds = array();
        $flashRootCategory = $this->_getFlashSaleCategory();

        if($flashRootCategory->getId()) {
            $childrenCategories = $flashRootCategory->getChildrenCategories();
            foreach($childrenCategories as $category) {
                $cIds[$category->getUrlKey()] = $category->getId();
            }
        }
        $cIds['flashsale'] = $flashRootCategory->getId();
        return $cIds;
    }

    /**
     * @return ProxiBlue_DynCatProd_Model_Category
     */
    protected function _getFlashSaleCategory()
    {
        /* @var $flashRootCategory ProxiBlue_DynCatProd_Model_Category */
        $flashRootCategory = Mage::getSingleton('catalog/category');
        return $flashRootCategory->loadByAttribute('url_key','flashsale');
    }

}
