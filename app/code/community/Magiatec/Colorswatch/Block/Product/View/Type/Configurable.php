<?php

/**
 * Class Magiatec_Colorswatch_Block_Product_View_Type_Configurable
 * @method Varien_Db_Statement_Pdo_Mysql getProductColorLabelStatement()
 */
class Magiatec_Colorswatch_Block_Product_View_Type_Configurable
    extends Mage_Catalog_Block_Product_View_Type_Configurable
{
    protected function _construct()
    {
        $this->addData(array(
            'cache_lifetime' => NULL,
        ));
    }

    /**
     * Overrides allowed products to all products in order to display out of stock options
     * @return array
     */
    public function getAllowProducts()
    {
        return $this->getAllProducts();
    }

    public function getAllProducts()
    {
        if (!$this->hasAllProducts()) {
            $products = array();
            $allProducts = $this->getProduct()->getTypeInstance(TRUE)
                ->getUsedProducts(NULL, $this->getProduct());
            /* @var $product Gri_CatalogCustom_Model_Product */
            foreach ($allProducts as $product) {
                if (!$product->isDisabled()) {
                    $products[] = $product;
                }
            }
            $this->setAllProducts($products);
        }
        return $this->getData('all_products');
    }

    public function getCacheKeyInfo()
    {
        return array_merge(parent::getCacheKeyInfo(), array(
            'product_id' => $this->getProduct()->getId(),
            'category_id' => Mage::registry('current_category') ? Mage::registry('current_category')->getId() : 0,
            'remove_unavailable_products' => Mage::registry('remove_unavailable_products'),
            'customer_group_id' => Mage::getSingleton('customer/session')->getCustomerGroupId(),
        ));
    }

    public function getJsonConfig()
    {
        $config = parent::getJsonConfig();

        $attributeIds = $optionIds = array();
        foreach ($this->getAllowAttributes() as $attribute) {
            $attributeIds[] = $attribute->getAttributeId();
            if (is_array($attribute->getPrices())) foreach ($attribute->getPrices() as $value) {
                $optionIds[] = $value['value_index'];
            }
        }

        $swatchesAttribute = Mage::getModel('magiatecolorswatch/attribute')
            ->getResourceCollection()
            ->addFieldToFilter('attribute_id', array('in' => $attributeIds));

        /* @var $swatchesProduct Magiatec_Colorswatch_Model_Resource_Product_Collection */
        $swatchesProduct = Mage::getModel('magiatecolorswatch/product')
            ->getResourceCollection()
            ->addFieldToFilter('value_index', array('in' => $optionIds));
        if ($this->getProduct()->getTypeInstance() instanceof Mage_Catalog_Model_Product_Type_Configurable) {
            $swatchesProduct->join(
                array('s' => 'catalog/product_super_attribute'),
                '`s`.`product_super_attribute_id` = `main_table`.`product_super_attribute_id`',
                array()
            )->addFieldToFilter('`s`.`product_id`', $this->getProduct()->getId())
            ->setOrder('image_id', $swatchesProduct::SORT_ORDER_DESC);
        }

        $config = Mage::helper('core')->jsonDecode($config); $productsIds = array();
        // Process in stock products
        $allowedProducts = array();
        foreach ($this->getRealAllowProducts() as $p) {
            $allowedProducts[$p->getId()] = $p->getId();
        }
        $config['allowedProducts'] = $allowedProducts;
        foreach ($config['attributes'] as $attrId => &$attribute) {
            $this->prepareProductColorLabel($attribute);
            foreach ($attribute['options'] as &$option) {
                $swatch = $swatchesAttribute->getItemByColumnValue('option_id', $option['id']);
                if ($swatch) {
                    $option['swatch'] = $this->helper('magiatecolorswatch')
                        ->getSwatchPath($swatch);
                }

                $swatch = $swatchesProduct->getItemByColumnValue('value_index', $option['id']);
                if ($swatch) {
                    $option['swatch'] = $this->helper('magiatecolorswatch')
                        ->getSwatchPath($swatch);
                }
                if ($attribute['code'] != 'color_code') {
                    $option['products'] = array_values(array_intersect($allowedProducts, $option['products']));
                }

                $productsIds = array_merge($productsIds, $option['products']);
            }
        }

        $config['attributeIdsToChange'] = explode(',', Mage::getStoreConfig('magiatecolorswatch/settings/attributes'));
        $config['swatchWidth'] = (int) Mage::getStoreConfig('magiatecolorswatch/settings/swatch_width');
        $config['swatchHeight'] = (int) Mage::getStoreConfig('magiatecolorswatch/settings/swatch_height');
        $config['showNotAvailable'] = (int) Mage::getStoreConfig('magiatecolorswatch/settings/show_not_available');


        $config['imageSwitcher']['enabled'] = (int) Mage::getStoreConfig('magiatecolorswatch/imageswitcher/enabled');
        /*
        if ($config['imageSwitcher']['enabled']) {
            $config['imageSwitcher']['images'] = $this->_getProductsImages($productsIds);
        }
        */

        $config['zoom']['enabled'] = (int) Mage::getStoreConfig('magiatecolorswatch/zoom/enabled');

        return Mage::helper('core')->jsonEncode($config);
    }

    public function getRealAllowProducts()
    {
        return parent::getAllowProducts();
    }

    public function int2Alphabet($int)
    {
        $alphabet = 'jabcdefghi';
        $result = '';
        $int = (string)$int;
        for ($i = 0, $count = strlen($int); $i < $count; ++$i) {
            $result .= $alphabet{(int)$int{$i}};
        }
        return $result;
    }

    public function prepareProductColorLabel(&$attribute)
    {
        if ($attribute['code'] != 'color_code') return $this;
        if (!$this->getProductColorLabelStatement()) {
            $resource = $this->getProduct()->getResource();
            $select = $resource->getReadConnection()->select();
            $colorLabelAttribute = $resource->getAttribute('color_label');
            $joinCondition = 'sv.attribute_id = dv.attribute_id AND sv.entity_id = dv.entity_id AND sv.store_id = ?';
            $joinCondition = $select->getAdapter()
                ->quoteInto($joinCondition, $this->getProduct()->getStoreId());
            $select->from(array('dv' => $colorLabelAttribute->getBackendTable()), array())
                ->joinLeft(array(
                    'sv' => $colorLabelAttribute->getBackendTable()
                ), $joinCondition, array())
                ->columns(array('value' => 'IFNULL(sv.value, dv.value)'))
                ->where('dv.store_id = 0')
                ->where('dv.attribute_id = ?', $colorLabelAttribute->getAttributeId());
            $select = $select . ' AND dv.entity_id = :id';
            $stmt = $resource->getReadConnection()->prepare($select);
            $this->setProductColorLabelStatement($stmt);
        }
        $stmt = $this->getProductColorLabelStatement();
        foreach ($attribute['options'] as &$v) if ($products = $v['products']) {
            $stmt->execute(array(':id' => reset($products)));
            $v['label'] = $stmt->fetchColumn();
        }
        return $this;
    }

    public function setProduct(Mage_Catalog_Model_Product $product)
    {
        $this->unsetData('allow_products')->unsetData('all_products');
        return $this->setData('product', $product);
    }

    /*
    protected function _getProductsImages($productsIds = array())
    {
        $productsIds = array_unique($productsIds);
        $images = array(); $_helper = Mage::helper('catalog/image');

        $images['sizes'] = array(
            'mainWidth'   => Mage::getStoreConfig('magiatecolorswatch/imageswitcher/width'),
            'mainHeight'  => Mage::getStoreConfig('magiatecolorswatch/imageswitcher/height'),
            'thumbWidth'  => Mage::getStoreConfig('magiatecolorswatch/imageswitcher/twidth'),
            'thumbHeight' => Mage::getStoreConfig('magiatecolorswatch/imageswitcher/theight'),
        );

        foreach ($productsIds as $productId) {
            $product = Mage::getModel('catalog/product')->load($productId);
            $images[$productId]['main'] = array(
                'resized'  => (string) $_helper->init($product, 'image')
                    ->resize($images['sizes']['mainWidth'], $images['sizes']['mainWidth']),
                'original' => (string) $_helper->init($product, 'image'),
                'label' => $product->getData('image_label') ? $product->getData('image_label') : $product->getName(),
            );

            $gallery = $product->getMediaGalleryImages();
            if($gallery->getSize()) {
                foreach($gallery as $image) {
                    $images[$productId]['thumbs'][] = array(
                        'thumb' => (string) $_helper->init($product, 'image', $image->getFile())
                            ->resize($images['sizes']['thumbWidth'], $images['sizes']['thumbWidth']),
                        'resized' => (string) $_helper->init($product, 'image', $image->getFile())
                            ->resize($images['sizes']['mainWidth'], $images['sizes']['mainWidth']),
                        'original' => (string) $_helper->init($product, 'image', $image->getFile()),
                        'label'    => $image->getLabel(),
                    );
                }
            } else {
                $images[$productId]['thumbs'] = array();
            }
        }
        return $images;
    }
    */
}
