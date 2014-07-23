<?php

class Gri_CatalogCustom_Helper_Product extends Mage_Core_Helper_Abstract
{
    protected $_productListItemBlock;

    protected $_sku;

    protected $_collection;

    protected $_urls = array();

    protected $_categoryCollection = NULL;

    protected $_maxSpecialPrice = array();

    /**
     * Retrieve Catalog Config object
     *
     * @return Mage_Catalog_Model_Config
     */
    public function getConfig()
    {
        return Mage::getSingleton('catalog/config');
    }

    public function getIsNew(Mage_Catalog_Model_Product $product)
    {
        $store = $product->getStore();
        $dateFrom = $product->getNewsFromDate();
        $dateTo = $product->getNewsToDate();
        return ($dateFrom || $dateTo) && Mage::app()->getLocale()->isStoreDateInInterval($store, $dateFrom, $dateTo);
    }

    public function getIsOnSale(Mage_Catalog_Model_Product $product)
    {
        Mage::log('price:' . $product->getPrice() . ' Final Price:' . $product->getFinalPrice() . ' MaxSpecialPrice:' . $this->getMaxSpecialPrice($product) . ' GroupPrice' . $product->getGroupPrice(), 7, 'price.log');
        return $product->getPrice() > $product->getFinalPrice() &&
            //(!$product->getSpecialPrice() || $product->getSpecialPrice() > $product->getFinalPrice()) &&
            $this->getMaxSpecialPrice($product) > $product->getFinalPrice() &&
            (!$product->getGroupPrice() || $product->getGroupPrice() > $product->getFinalPrice());
    }

    public function getMaxSpecialPrice($product)
    {
        if(!isset($this->_maxSpecialPrice[$product->getId()])){
            $typeId = $product->getTypeId();
            $maxSpecialPrice = 0.00;
            switch ($typeId) {
                case Mage_Catalog_Model_Product_Type_Configurable::TYPE_CODE :
                    $prices = $this->getPricesBySku($product->getSku());
                    if (count($prices)) {
                        foreach ($prices as $price) {
                            $price['special_price'] > $maxSpecialPrice && $maxSpecialPrice = $price['special_price'];
                        }
                    }
                break;

                case Gri_CatalogCustom_Model_Product_Type_Simple::TYPE_CODE :
                    $maxSpecialPrice = $product->getSpecialPrice();
                    break;

                default:
                    $maxSpecialPrice = 0.00;
            }

            $this->_maxSpecialPrice[$product->getId()] = $maxSpecialPrice;
        }

        return $this->_maxSpecialPrice[$product->getId()];
    }

    /**
     * @param $sku  configurable product's sku
     *
     * @return Array
     */
    public function getPricesBySku($sku = NULL)
    {
        /* @var $configurableProduct Mage_Catalog_Model_Product */
        $configurableProduct = Mage::helper('gri_catalogcustom')->getProductBySku($sku);
        $configurableProduct = $configurableProduct && $configurableProduct->getTypeId() == Mage_Catalog_Model_Product_Type_Configurable::TYPE_CODE ? $configurableProduct : FALSE;

        $simpleProducts = $configurableProduct ? Mage::getModel('catalog/product_type_configurable')
            ->setProduct($configurableProduct)
            ->getUsedProductCollection()
            ->addAttributeToSelect(array('price', 'special_price'))
            ->addFilterByRequiredOptions() : array();

        $prices = array();
        foreach ($simpleProducts as $_product) {
            $prices[$_product->getId()] = array('price' => $_product->getPrice(),
                'special_price' => $_product->getSpecialPrice());
        }

        // add configurable product price & special_price
        $configurableProduct and $prices[$configurableProduct->getId()] = array('price' => $configurableProduct->getPrice(),
            'special_price' => $configurableProduct->getSpecialPrice());

        if (count($prices) > 1) {
            $maxPrice = 0;
            $maxSpecialPrice = 0;
            foreach ($prices as $price) {
                $price['price'] > $maxPrice and $maxPrice = $price['price'];
                $price['special_price'] > $maxSpecialPrice and $maxSpecialPrice = $price['special_price'];
            }

            foreach ($prices as $key => $price) {
                $prices[$key]['price'] = $maxPrice;
                $prices[$key]['special_price'] = $maxSpecialPrice;
            }
        }

        return $prices;
    }

    public function getIsPreorder(Mage_Catalog_Model_Product $product)
    {
        return Mage::getSingleton('gri_preorder/preorder')->isPreorder($product);
    }

    public function getIsPresale(Mage_Catalog_Model_Product $product)
    {
        /* @var $rule Gri_Presale_Model_Rule */
        $rule = Mage::getSingleton('gri_presale/rule');
        return $rule->isEnabled() && $rule->getIsPresaleProduct($product) &&
            $rule->getRuleProduct($product) && $product->getFinalPrice() != $product->getPreSalePrice();
    }

    /**
     * @return Gri_CatalogCustom_Block_Product_List_Item
     */
    public function getProductListItemBlock()
    {
        $this->_productListItemBlock or $this->_productListItemBlock =
            Mage::app()->getLayout()->createBlock('gri_catalogcustom/product_list_item');
        return $this->_productListItemBlock;
    }

    public function getRewardPointsHtml(Mage_Catalog_Model_Product $product)
    {
        if ($product->getRewardPointsHtml() === NULL) {
            $product->setRewardPointsHtml(
                $this->__('%s points', Zend_Locale_Format::toNumber($product->getRewardPoints(), array('locale' => Mage::app()->getLocale()->getLocaleCode(), 'precision' => 0)))
            );
        }
        return $product->getRewardPointsHtml();
    }

    public function getRibbonClass(Mage_Catalog_Model_Product $product)
    {
        if ($product->getRibbonClass() === NULL) {
            $ribbons = array(
                'new-arrival' => $this->getIsNew($product),
             // 'on_sale' => $this->getIsOnSale($product),
                'on_sale' => FALSE,
                'flash_sale' => $product->getIsFlashSale(),
                'sold_out' => !$product->isSalable(),
                'presale' => $this->getIsPresale($product),
                'preorder' => $this->getIsPreorder($product),
            );
            $classes = array();
            if (array_sum($ribbons)) {
                $classes[] = 'special_offer';
            }

            if ($ribbons['sold_out']) $classes[] = 'sold-out-ribbon';
            else if ($ribbons['preorder']) $classes[] = 'preorder-ribbon';
            else if ($ribbons['flash_sale']) $classes[] = 'flash-sale-ribbon';
            else if ($ribbons['presale']) $classes[] = 'presale-ribbon';
            else if ($ribbons['on_sale']) $classes[] = 'on-sale-ribbon';
            else if ($ribbons['new-arrival']) $classes[] = 'new-arrival-ribbon';

            $product->setRibbonClass($classes);
        }


        return $product->getRibbonClass();
    }

    public function getRibbonLabel(Mage_Catalog_Model_Product $product)
    {
        if ($product->getRibbonLabel() === NULL) {
            $labels = array(
                'new-arrival-ribbon' => 'New Arrival',
                'on-sale-ribbon' => 'On Sale',
                'flash-sale-ribbon' => 'Flash Sale',
                'sold-out-ribbon' => 'Sold Out',
                'presale-ribbon' => 'Pre Sale',
                'preorder-ribbon' => 'Pre Order',
                'freegift-ribbon' => 'Free Gift'
            );
            $classes = $this->getRibbonClass($product);
            $label = end($classes);
            isset($labels[$label]) and $label = $labels[$label];
            $product->setRibbonLabel($label);
        }
        return $product->getRibbonLabel();
    }

    public function renderProduct($product)
    {
        return $this->getProductListItemBlock()->setProduct($product)->toHtml();
    }

    public function isGift(Mage_Catalog_Model_Product $product)
    {
        return $product->getTypeId() == 'giftcard' ||
            $product->getAttributeSetId() == $this->getConfig()->getAttributeSetId('catalog_product', 'Gifts');
    }

    /**
     *  Getter All Configurable Products Sku
     * @return Array
     */
    public function getAllCPSku()
    {
        try {
            if (!count($this->_sku)) {
                $sql = "SELECT `sku` FROM `catalog_product_entity` WHERE `type_id`='configurable'";
                $this->_sku = Mage::getSingleton('core/resource')->getConnection('core_read')
                    ->fetchCol($sql);
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return $this->_sku;
    }

    public function getNavUrl(Gri_CatalogCustom_Model_Product $product)
    {
        $product = $product ? $product : Mage::registry('current_product');
        $categoryId = $product->getCategoryIdFromBreadcrumbPath();
        if ($categoryId && $product && !isset($this->_urls[$product->getId()])) {
            /* @var $productCategory Gri_CatalogCustom_Model_Category */
            $productCategory = Mage::getModel('catalog/category')->load(intval($categoryId));
            /* @var $collection ProxiBlue_DynCatProd_Model_Resource_Product_Collection */
            $collection = $productCategory->getProductCollection()
                           ->addAttributeToSelect('*')
                           ->addAttributeToFilter('type_id', Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE)
                           ->addUrlRewrite($productCategory->getId())
                           ->addAttributeToSort('created_at', Varien_Data_Collection::SORT_ORDER_DESC);

            Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);

            $productIds = $collection->getAllIds();

            $flipProductIds = array_flip($productIds);
            $currentProductIndex = isset($flipProductIds[$product->getId()]) ? $flipProductIds[$product->getId()] : 0;


            $sProduct = Mage::getSingleton('catalog/product');

            //Pre URL
            $this->_urls[$product->getId()]['pre'] = $currentProductIndex && isset($productIds[$preIndex=$currentProductIndex + 1]) ? $sProduct->unsetData()->load($productIds[$preIndex])->getProductUrl() : '#';

            //Next URL
            $this->_urls[$product->getId()]['next'] = $currentProductIndex && isset($productIds[$nextIndex=$currentProductIndex - 1]) ? $sProduct->unsetData()->load($productIds[$nextIndex])->getProductUrl() : '#';
        }

        return $categoryId != Mage::app()->getStore()->getRootCategoryId() ? $this->_urls[$product->getId()] : array();
    }

    public function getCategoryId(Mage_Catalog_Model_Product $product)
    {
        return Mage::getResourceModel('catalog/product')->getCategoryIds($product);
    }

    /**
     * @param array $catIds
     * @param int $child_count
     * @return null | Mage_Catalog_Model_Resource_Category_Collection
     */
    public function getFinalCategoryIds($catIds = array(), $children_count = 0)
    {
        if ($this->_categoryCollection == NULL) {
            /* @var $collection Mage_Catalog_Model_Resource_Category_Collection */
            $this->_categoryCollection = Mage::getSingleton('catalog/category')
                ->getCollection()
                ->addAttributeToSelect('*')
                ->addIdFilter($catIds)
                ->addFieldToFilter('children_count', $children_count);
        }
        return $this->_categoryCollection;
    }

}
