<?php

class Gri_ImportData_Model_Convert_Adapter_Product extends Mage_Catalog_Model_Convert_Adapter_Product
{
    protected $_allStoreIds;
    protected $_attributesMap;
    protected $_categories = array();
    protected $_defaultStoreId;
    protected $_ignoredAttributes = array(
        'price_type', 'sku_type', 'weight_type', 'shipment_type',
        'links_purchased_separately', 'samples_title', 'links_title',
        'allow_open_amount', 'giftcard_type', 'price_view',
    );
    protected $_productSingleton;
    protected $_setup;

    public function __construct()
    {
        parent::__construct();
        Mage::app()->getLocale()->setLocaleCode('en_US');
        Mage::getSingleton('core/translate')->setLocale(Mage::app()->getLocale()->getLocale())->init('frontend', true);
    }

    protected function _getAllStoreIds() {
        if ($this->_allStoreIds === NULL)
            $this->_allStoreIds = array_keys(Mage::app()->getStores());
        return $this->_allStoreIds;
    }

    protected function _getDefaultStoreId() {
        if ($this->_defaultStoreId === NULL)
            $this->_defaultStoreId = Mage::app()->getDefaultStoreView()->getId();
        return $this->_defaultStoreId;
    }

    /**
     * @return Gri_ImportData_Helper_Data
     */
    protected function _getHelper() {
        return Mage::helper('gri_importdata');
    }

    /**
     * @return Gri_CatalogCustom_Model_Product
     */
    protected function _getProductSingleton() {
        if ($this->_productSingleton === NULL)
            $this->_productSingleton = Mage::getModel('catalog/product');
        return $this->_productSingleton;
    }

    protected function _getSetup() {
        if ($this->_setup === NULL)
            $this->_setup = new Mage_Catalog_Model_Resource_Setup('catalog_setup');
        return $this->_setup;
    }

    /**
     * @param Mage_Catalog_Model_Entity_Attribute $attribute
     * @param string $adminLabel
     * @param string $defaultStoreLabel
     * @param string $universalLabel
     */
    public function addAttributeOption($attribute, $adminLabel, $defaultStoreLabel = NULL, $universalLabel = NULL) {
        $setup = $this->_getSetup();
        $option = array();
        $option['attribute_id'] = $attribute->getId();
        // $option['value'][$optionId][$storeId], $optionId = 0 means create new option
        $option['value'][0][0] = $adminLabel;
        $universalLabel or $universalLabel = $defaultStoreLabel;
        $defaultStoreLabel or $defaultStoreLabel = $universalLabel;
        if ($universalLabel) {
            foreach ($this->_getAllStoreIds() as $storeId) {
                $option['value'][0][$storeId] = $storeId == $this->_getDefaultStoreId() ? $defaultStoreLabel : $universalLabel;
            }
        }
        $attribute->unsetData('options');
        $setup->addAttributeOption($option);
    }

    public function checkUrlKeyExist($urlKey) {
        return $this->_getProductSingleton()->loadByAttribute('url_key', $urlKey);
    }

    /**
     * @param Mage_Catalog_Model_Entity_Attribute $attribute
     * @return array
     */
    public function getAttributeOptions($attribute) {
        if ($attribute->getData('options') === NULL) {
            $attribute->setData('options', $attribute->getSource()->getAllOptions(FALSE));
        }
        return $attribute->getData('options');
    }

    /**
     * @param string $parentPath
     * @param string $name
     * @return Gri_CatalogCustom_Model_Category
     */
    public function getCategory($parentPath, $name) {
        if (!isset($this->_categories[$key = $parentPath . '-' . $name])) {
            $this->_categories[$key] = FALSE;
            /* @var $categories Mage_Catalog_Model_Resource_Category_Collection */
            $categories = Mage::getSingleton('catalog/category')->getCollection();

            $parentPath = array_reverse(explode('/',$parentPath)) ;
            $parentId = $parentPath[0];
            $categories->addFieldToFilter('name', array('eq' => array($name)))
                // ->addFieldToFilter('path', array('like' => array($parentPath . '/%')))
                ->addFieldToFilter('parent_id', array('eq' => array($parentId)))
                ->setPageSize(1);

            $categories->count() and $this->_categories[$key] = $categories->getFirstItem();
        }
        return $this->_categories[$key];
    }


    public function getIgnoredAttributes(Mage_Catalog_Model_Product $product) {
        $ignoredAttributes = array_flip(array_merge(
            $product->getResource()->getDefaultAttributes(), $this->_ignoredAttributes
        ));
        return $ignoredAttributes;
    }

    public function getProductAttributes(Mage_Catalog_Model_Product $product) {
        $attributes = $product->getResource()->getSortedAttributes($product->getAttributeSetId());
        $ignoredAttributes = $this->getIgnoredAttributes($product);
        foreach ($attributes as $code => $attribute) {
            if (isset($ignoredAttributes[$code]))
                unset($attributes[$code]);
        }
        return $attributes;
    }

    public function getStoreByCode($store) {
        if($store == 'hk_eng'){
            $store = 'admin';
        }
        if (!isset($this->_stores[$store])) {
            $this->_stores[$store] = parent::getStoreByCode($store);
        }
        return $this->_stores[$store];
    }

    public function saveRow(array $importData) {
        $maps = $this->_getHelper()->importAttributesMap();
        $newImportData = array();
        foreach ($importData as $k => $v) {
            if (isset($maps[$k]) && !isset($newImportData[$maps[$k]]))
                $newImportData[$maps[$k]] = $v;
            else
                $newImportData[$k] = $v;
        }
        $importData = array_merge($this->_getHelper()->defaultAttributeValue(), $newImportData);
        $product = $this->getProductModel()->reset();
        $message = '';

        if ($importData['sku'])
            $message .= "[SKU:" . $importData['sku'] . "] ";
        else {
            $message .= "[SKU:] ";
            $message .= $this->_getHelper()->__('Skipping import row, required field "%s" is not defined.', 'sku');
            $this->logAndThrowMessage($message);
        }

        if (empty($importData['store'])) {
            if (!is_null($this->getBatchParams('store'))) {
                $store = $this->getStoreById($this->getBatchParams('store'));
            } else {
                $message .= $this->_getHelper()->__('Skipping import row, required field "%s" is not defined.', 'store');
                $this->logAndThrowMessage($message);
            }
        } else {
            $store = $this->getStoreByCode($importData['store']);
        }

        if ($store === false) {
            $message .= $this->_getHelper()->__('Skipping import row, store "%s" field does not exist.', $importData['store']);
            $this->logAndThrowMessage($message);
        }

        $product->setStoreId($store->getId());
        $productId = Mage::getResourceSingleton('catalog/product')->getIdBySku($importData['sku']);

        if ($importData['type'] == 'simple') {
            if (!$productId && $this->getBatchParams('mode') == 'update'){
                $message .= $this->_getHelper()->__('Skipping import row, product "%s" was not found for update mode.', $importData['sku']);
                $this->logAndThrowMessage($message);
            }
            else if ($productId && $this->getBatchParams('mode') != 'update'){
                $message .= $this->_getHelper()->__('Skipping import row, product "%s" already exists and cannot be recreated.', $importData['sku']);
                $this->logAndThrowMessage($message);
            }
        }


        if ($productId) {
            $product->load($productId);
        } else {
            $productTypes = $this->getProductTypes();
            $productAttributeSets = $this->getProductAttributeSets();

            /**
             * Check product define type
             */
            if (empty($importData['type']) || !isset($productTypes[strtolower($importData['type'])])) {
                $message .= $this->_getHelper()->__('Skip import row, is not valid value "%s" for field "%s"', $importData['type'], 'type');
                $this->logAndThrowMessage($message);
            }
            $product->setTypeId($productTypes[strtolower($importData['type'])]);

            /**
             * Check product define attribute set
             */
            if (!isset($importData['attribute_set']) || !isset($productAttributeSets[$importData['attribute_set']])) {
                $value = isset($importData['attribute_set']) ? $importData['attribute_set'] : '';
                $message .= $this->_getHelper()->__('Skip import row, the value "%s" is invalid for field "%s"', $value, 'attribute_set');
                $this->logAndThrowMessage($message);
            }
            $product->setAttributeSetId($productAttributeSets[$importData['attribute_set']]);
        }

        // Check required attributes
        foreach ($this->getProductAttributes($product) as $attribute) {
            $field = $attribute->getAttributeCode();
            if ((!isset($importData[$field]) || $importData[$field] === '') && $attribute->getIsRequired()) {
                $message .= $this->_getHelper()->__('Skipping import row, required field "%s" is not defined.', $field);
                $this->logAndThrowMessage($message);
            }
            if ($attribute->getFrontendInput() == 'date' && !empty($importData[$field])) {
                $importData[$field] = Mage::app()->getLocale()->date($importData[$field], Zend_Date::ISO_8601);
            }
        }
        $importData['url_key'] = $product->formatUrlKey($importData['brand'] . '-' . $importData['sku']);
        $this->setProductTypeInstance($product);

        // Assign according categories
        $rootCategory = 2;
        $rootPath = '1/2';
        $categoryIds = array($rootCategory);
        if (!isset($importData['category_ids'])) {

            // Brand root
            if ($brandCategory = $this->getCategory($rootPath, $importData['brand'])) {
                $categoryIds[] = $brandCategory->getId();
                $brandPath = $brandCategory->getPath();
            }

            // Level 1: Shoes
            if (isset($importData['category1'])) {
                if ($shop_level1 = $this->getCategory($rootPath, $importData['category1'])) {
                    $shop_level1_Path = $shop_level1->getPath();
                    $categoryIds[] = $shop_level1->getId();
                }

                // Brand >> Shop
                if (isset($brandPath))
                    $shopBrand = $this->getCategory($brandPath, 'Shop');
                if (isset($shopBrand) && $shopBrand) {
                    $categoryIds[] = $shopBrand->getId();
                    $shopBrandPath = $shopBrand->getPath();
                }

                // Brand level 1: Brand >> Shop >> Shoes
                if (isset($shopBrandPath) && $shopBrandPath) {
                    $brand_level1 = $this->getCategory($shopBrandPath, $importData['category1']);
                    if ($brand_level1) {
                        $brand_level1_path = $brand_level1->getPath();
                        $categoryIds[] = $brand_level1->getId();
                    }
                }

                // Level 2: Shoes >> Pumps
                if (isset($importData['category2']) && isset($shop_level1_Path) && isset($brand_level1_path)) {
                    $shop_level2 = $this->getCategory($shop_level1_Path, $importData['category2']);
                    if ($shop_level2) {
                        $shop_level2_Path = $shop_level2->getPath();
                        $categoryIds[] = $shop_level2->getId();
                    }
                    // Brand level 2: Brand >> Shop >> Shoes >> Pumps
                    $brand_level2 = $this->getCategory($brand_level1_path, $importData['category2']);
                    if ($brand_level2) {
                        $brand_level2_path = $brand_level2->getPath();
                        $categoryIds[] = $brand_level2->getId();
                    }

                    // Level 3: Shoes >> Pumps >> Mid
                    if (isset($importData['category3']) && isset($shop_level2_Path) && isset($brand_level2_path)) {
                        $shop_level3 = $this->getCategory($shop_level2_Path, $importData['category3']);
                        if ($shop_level3)
                            $categoryIds[] = $shop_level3->getId();
                        // Brand level 3: Brand >> Shop >> Shoes >> Pumps >> Mid
                        $brand_level3 = $this->getCategory($brand_level2_path, $importData['category3']);
                        if ($brand_level3)
                            $categoryIds[] = $brand_level3->getId();
                    }
                }
            }
            // end assign category
            $importData['category_ids'] = implode(',', $categoryIds);
        }
        $product->setCategoryIds($importData['category_ids']);
        foreach ($this->_ignoreFields as $field) {
            if (isset($importData[$field]) && $field != 'attribute_set' && $field != 'type') {
                unset($importData[$field]);
            }
        }

        if ($store->getId() != 0) {
            $websiteIds = $product->getWebsiteIds();
            if (!is_array($websiteIds)) {
                $websiteIds = array();
            }
            if (!in_array($store->getWebsiteId(), $websiteIds)) {
                $websiteIds[] = $store->getWebsiteId();
            }
            $product->setWebsiteIds($websiteIds);
        }

        if (isset($importData['websites'])) {
            $websiteIds = $product->getWebsiteIds();
            if (!is_array($websiteIds) || !$store->getId()) {
                $websiteIds = array();
            }
            $websiteCodes = explode(',', $importData['websites']);
            foreach ($websiteCodes as $websiteCode) {
                try {
                    $website = Mage::app()->getWebsite(trim($websiteCode));
                    if (!in_array($website->getId(), $websiteIds)) {
                        $websiteIds[] = $website->getId();
                    }
                } catch (Exception $e) {

                }
            }
            $product->setWebsiteIds($websiteIds);
            unset($websiteIds);
        }

        foreach ($importData as $field => $value) {
            if (in_array($field, $this->_inventoryFields)) {
                continue;
            }
            if (is_null($value)) {
                continue;
            }

            $attribute = $this->getAttribute($field);
            if (!$attribute) {
                continue;
            }

            $isArray = false;
            $setValue = $value;

            if ($attribute->getFrontendInput() == 'multiselect') {
                $value = explode(self::MULTI_DELIMITER, $value);
                $isArray = true;
                $setValue = array();
            }

            if ($value && $attribute->getBackendType() == 'decimal') {
                $setValue = $this->getNumber($value);
            }

            if ($attribute->usesSource() && $value != '') {
                $options = $this->getAttributeOptions($attribute);
                if ($isArray) {
                    foreach ($options as $item) {
                        if (in_array($item['label'], $value)) {
                            $setValue[] = $item['value'];
                        }
                    }
                } else {
                    $setValue = false;
                    $exist = false;
                    $value = trim($value);
                    foreach ($options as $item) {
                        if (is_array($item['value'])) {
                            foreach ($item['value'] as $subValue) {
                                if (isset($subValue['value']) && strtolower($subValue['value']) === strtolower($value)) {
                                    $setValue = $subValue['value'];
                                    $exist = true;
                                    break;
                                }
                            }
                        } else if (strtolower($item['label']) === strtolower($value)) {
                            $setValue = $item['value'];
                            $exist = true;
                            break;
                        }
                    }
                    // for option, will create if not exist
                    if (!$exist) {
                        // create new one for color_code
                        if ($attribute->getAttributeCode() == 'color_code') {
                            $this->addAttributeOption($attribute, $value);
                            /* @var $optionCollection Mage_Eav_Model_Resource_Entity_Attribute_Option_Collection */
                            $optionCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
                                ->setAttributeFilter($attribute->getId())
                                ->setStoreFilter(0, false)
                                ->addFieldToFilter('tsv.value', $value)
                                ->load();
                            foreach ($optionCollection as $_opt) {
                                if ($_opt->getValue() == $value) {
                                    $options[] = array(
                                        'label' => $value,
                                        'value' => $setValue = $_opt->getId(),
                                    );
                                    $attribute->setOptions($options);
                                    break;
                                }
                            }
                        } else {
                            // Throw error directly if option is not exist
                            $message .= $this->_getHelper()->__('Skipping import row, "%s" value "%s" is not defined.', $field, $value);
                            $this->logAndThrowMessage($message);
                        }
                    }
                }
            }
            //end option oparation
            $product->setData($field, $setValue);
        }

        if ($importData['type'] == 'configurable')
            unset($importData['configurable']);

        if (isset($importData['configurable']) && $importData['configurable'] || !$product->getVisibility()) {
            $product->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE);
        }
        // store inventory data only for new product
        if (!$productId || $this->_getHelper()->canUpdateInventory()) {
            $productId || $this->_getHelper()->canCreateInventory() or $importData['qty'] = 0;
            $stockData = array();
            $inventoryFields = isset($this->_inventoryFieldsProductTypes[$product->getTypeId()]) ? $this->_inventoryFieldsProductTypes[$product->getTypeId()] : array();
            foreach ($inventoryFields as $field) {
                if (isset($importData[$field])) {
                    if (in_array($field, $this->_toNumber)) {
                        $stockData[$field] = $this->getNumber($importData[$field]);
                    } else {
                        $stockData[$field] = $importData[$field];
                    }
                }
            }
            $product->setStockData($stockData);
        }

        // end inventroy
        $product->setIsMassupdate(true);
        $product->setExcludeUrlRewrite(true);

        // TODO create configurable product
        if ($importData['type'] == 'configurable') {
            /* @var $typeInstance Gri_CatalogCustom_Model_Product_Type_Configurable */
            $typeInstance = $product->getTypeInstance();
            $typeInstance->setUsedProductAttributeIds(
                $this->_getHelper()->getConfigAttributeIds($importData['attribute_set'])
            );
            $configurable_attributes_data = $typeInstance->getConfigurableAttributesAsArray($product);
            foreach ($configurable_attributes_data as $k => $v) {
                $configurable_attributes_data[$k]['use_default'] = 1;
            }
            if (!$productId)
                $product->setConfigurableAttributesData($configurable_attributes_data);

            //assing product id
            $super_product_ids = $typeInstance->getUsedProductIds();
            $productIds = array();
            if ($super_product_ids)
                foreach ($super_product_ids as $v)
                    $productIds[$v] = $v;
            if (isset($importData['config_product_id']) && !isset($productIds[$importData['config_product_id']]))
                $productIds[$importData['config_product_id']] = $importData['config_product_id'];
            $product->setConfigurableProductsData($productIds);
        }

        // add group price
        $importData['type'] == 'configurable'  &&  $product->addGroupPrice();

        try {
            $product->save();
        } catch (Exception $e) {
            Mage::logException($e);
            $message .= $this->_getHelper()->__('Internal server error on saving product. Message: '.$e->getMessage());
            Mage::throwException($message);
        }

        if (isset($importData['configurable']) && $importData['configurable'] && $importData['style_name']) {
            $importData['sku'] = $importData['style_name'];
            $importData['type'] = 'configurable';
            $importData['config_product_id'] = $product->getId();
            $importData['price'] = $importData['max_price'];
            $importData['special_price'] = '' ;// $importData['max_special_price'];
            $this->saveRow($importData);
        }
        // Store affected products ids
        $this->_addAffectedEntityIds($product->getId());

        return true;
    }

    protected function logAndThrowMessage($msg)
    {
        Mage::log($msg, NULL, 'import.exception.log');
        Mage::throwException($msg);
    }
}
