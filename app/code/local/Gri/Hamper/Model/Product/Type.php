<?php

class Gri_Hamper_Model_Product_Type extends Mage_Bundle_Model_Product_Type
{
    const TYPE_HAMPER = 'hamper';

    /**
     * @return Gri_Hamper_Model_Resource_Selection
     */
    protected function _getSelectionSingleton()
    {
        return Mage::getResourceSingleton('hamper/selection');
    }

    protected function _prepareProduct(Varien_Object $buyRequest, $product, $processMode)
    {
        $product->setBuyRequest($buyRequest);
        $result = Mage_Catalog_Model_Product_Type_Abstract::_prepareProduct($buyRequest, $product, $processMode);

        if (is_string($result)) {
            return $result;
        }

        $selections = array();
        $product = $this->getProduct($product);
        $isStrictProcessMode = $this->_isStrictProcessMode($processMode);

        $skipSaleableCheck = Mage::helper('catalog/product')->getSkipSaleableCheck();
        $_appendAllSelections = (bool)$product->getSkipCheckRequiredOption() || $skipSaleableCheck;

        $options = $buyRequest->getBundleOption();
        if (is_array($options)) {
            $options = array_filter($options, 'intval');
            $qtys = $buyRequest->getBundleOptionQty();
            foreach ($options as $_optionId => $_selections) {
                if (empty($_selections)) {
                    unset($options[$_optionId]);
                }
            }
            $optionIds = array_keys($options);

            if (empty($optionIds) && $isStrictProcessMode) {
                return Mage::helper('bundle')->__('Please select options for product.');
            }

            $product->getTypeInstance(TRUE)->setStoreFilter($product->getStoreId(), $product);
            $optionsCollection = $this->getOptionsCollection($product);
            if (!$this->getProduct($product)->getSkipCheckRequiredOption() && $isStrictProcessMode) {
                foreach ($optionsCollection->getItems() as $option) {
                    if ($option->getRequired() && !isset($options[$option->getId()])) {
                        return Mage::helper('bundle')->__('Required options are not selected.');
                    }
                }
            }
            $selectionIds = array();

            foreach ($options as $optionId => $selectionId) {
                if (!is_array($selectionId)) {
                    if ($selectionId != '') {
                        $selectionIds[] = (int)$selectionId;
                    }
                } else {
                    foreach ($selectionId as $id) {
                        if ($id != '') {
                            $selectionIds[] = (int)$id;
                        }
                    }
                }
            }
            // If product has not been configured yet then $selections array should be empty
            if (!empty($selectionIds)) {
                $selections = $this->getSelectionsByIds($selectionIds, $product);
                $pQtys = array();
                foreach ($product->getData($this->_keyUsedSelectionsIds) as $pid) {
                    $pQtys[$pid] = isset($pQtys[$pid]) ? $pQtys[$pid] + 1 : 1;
                }

                // Check if added selections are still on sale
                foreach ($selections->getItems() as $key => $selection) {
                    if (!$selection->isSalable() && !$skipSaleableCheck) {
                        return Mage::helper('bundle')->__('Selected options are not available.');
                    }
                }

                $optionsCollection->appendSelections($selections, FALSE, $_appendAllSelections);

                $selections = $selections->getItems();
            } else {
                $selections = array();
            }
        } else {
            $product->setOptionsValidationFail(TRUE);
            $product->getTypeInstance(TRUE)->setStoreFilter($product->getStoreId(), $product);

            $optionCollection = $product->getTypeInstance(TRUE)->getOptionsCollection($product);

            $optionIds = $product->getTypeInstance(TRUE)->getOptionsIds($product);
            $selectionIds = array();

            $selectionCollection = $product->getTypeInstance(TRUE)
                ->getSelectionsCollection(
                    $optionIds,
                    $product
                );

            $options = $optionCollection->appendSelections($selectionCollection, FALSE, $_appendAllSelections);

            foreach ($options as $option) {
                if ($option->getRequired() && count($option->getSelections()) == 1) {
                    $selections = array_merge($selections, $option->getSelections());
                } else {
                    $selections = array();
                    break;
                }
            }
        }
        if (count($selections) > 0 || !$isStrictProcessMode) {
            $uniqueKey = array($product->getId());
            $selectionIds = array();

            /* @var $selection Mage_Catalog_Model_Product */
            foreach ($selections as $selection) {
                $qty = isset($pQtys[$selection->getId()]) ? $pQtys[$selection->getId()] : 1;

                $product->addCustomOption('selection_qty_' . $selection->getSelectionId(), $qty, $selection);
                $selection->addCustomOption('selection_id', $selection->getSelectionId());

                $beforeQty = 0;
                $customOption = $product->getCustomOption('product_qty_' . $selection->getId());
                if ($customOption) {
                    $beforeQty = (float)$customOption->getValue();
                }
                $product->addCustomOption('product_qty_' . $selection->getId(), $qty + $beforeQty, $selection);

                /*
                 * Create extra attributes that will be converted to product options in order item
                 * for selection (not for all bundle)
                 */
                $price = $selection->getPrice() * $qty;
                $attributes = array(
                    'price' => Mage::app()->getStore()->convertPrice($price),
                    'qty' => $qty,
//                    'option_label'  => $selection->getOption()->getTitle(),
//                    'option_id'     => $selection->getOption()->getId()
                );

                $_result = $selection->getTypeInstance(TRUE)->prepareForCart($buyRequest, $selection);
                if (is_string($_result) && !is_array($_result)) {
                    return $_result;
                }

                if (!isset($_result[0])) {
                    return Mage::helper('checkout')->__('Cannot add item to the shopping cart.');
                }

                $result[] = $_result[0]->setParentProductId($product->getId())
                    ->addCustomOption('bundle_option_ids', serialize(array_map('intval', $optionIds)))
                    ->addCustomOption('bundle_selection_attributes', serialize($attributes));

                if ($isStrictProcessMode) {
                    $_result[0]->setCartQty($qty);
                }

                $selectionIds[] = $_result[0]->getSelectionId();
                $uniqueKey[] = $_result[0]->getSelectionId();
                $uniqueKey[] = $qty;
            }

            // "unique" key for bundle selection and add it to selections and bundle for selections
            $uniqueKey = implode('_', $uniqueKey);
            foreach ($result as $item) {
                $item->addCustomOption('bundle_identity', $uniqueKey);
            }
            $product->addCustomOption('bundle_option_ids', serialize(array_map('intval', $optionIds)));
            $product->addCustomOption('bundle_selection_ids', serialize($selectionIds));

            return $result;
        }

        return $this->getSpecifyOptionMessage();
    }

    public function checkProductBuyState($product = NULL)
    {
        Mage_Catalog_Model_Product_Type_Abstract::checkProductBuyState($product);
        return $this;
    }

    public function getChildrenIds($parentId, $required = TRUE)
    {
        return $this->_getSelectionSingleton()->getChildrenIds($parentId, $required);
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @return boolean
     */
    public function getIsPreset($product = NULL)
    {
        return $this->getProduct($product)->getPriceType() == Gri_Hamper_Model_Product_Price::PRICE_TYPE_FIXED;
    }

    /**
     * Retrieve bundle option collection
     *
     * @param Mage_Catalog_Model_Product $product
     * @return Mage_Bundle_Model_Resource_Option_Collection
     */
    public function getOptionsCollection($product = NULL)
    {
        if (!$this->getProduct($product)->hasData($this->_keyOptionsCollection)) {
            /* @var $optionsCollection Mage_Bundle_Model_Resource_Option_Collection */
            $optionsCollection = Mage::getModel('bundle/option')->getResourceCollection();
            $optionsCollection->setProductIdFilter($this->getProduct($product)->getId())
                ->setPositionOrder();

            $storeId = $this->getStoreFilter($product);
            if ($storeId instanceof Mage_Core_Model_Store) {
                $storeId = $storeId->getId();
            }

            $optionsCollection->joinValues($storeId);
            $image = $optionsCollection->getConnection()->getCheckSql(
                'option_value.image IS NOT NULL',
                'option_value.image',
                'option_value_default.image'
            );
            $optionsCollection->addExpressionFieldToSelect('image', $image, array());
            $storeId === NULL or $optionsCollection->addExpressionFieldToSelect('default_image', 'option_value_default.image', array());
            $this->getProduct($product)->setData($this->_keyOptionsCollection, $optionsCollection);
        }
        return $this->getProduct($product)->getData($this->_keyOptionsCollection);
    }

    public function getOrderOptions($product = NULL)
    {
        $optionArr = Mage_Catalog_Model_Product_Type_Abstract::getOrderOptions($product);

        $bundleOptions = array();

        $product = $this->getProduct($product);

        if ($product->hasCustomOptions()) {
            $customOption = $product->getCustomOption('bundle_option_ids');
            $optionIds = unserialize($customOption->getValue());
//            $options = $this->getOptionsByIds($optionIds, $product);
            $customOption = $product->getCustomOption('bundle_selection_ids');
            $selectionIds = unserialize($customOption->getValue());
            $selections = $this->getSelectionsByIds($selectionIds, $product);
            foreach ($selections->getItems() as $selection) {
                if ($selection->isSalable()) {
                    $selectionQty = $product->getCustomOption('selection_qty_' . $selection->getSelectionId());
                    if ($selectionQty) {
                        $price = $product->getPriceModel()->getSelectionFinalTotalPrice($product, $selection, 0,
                            $selectionQty->getValue()
                        );

                        /*$option = $options->getItemById($selection->getOptionId());
                        if (!isset($bundleOptions[$option->getId()])) {
                            $bundleOptions[$option->getId()] = array(
                                'option_id' => $option->getId(),
                                'label' => $option->getTitle(),
                                'value' => array()
                            );
                        }

                        $bundleOptions[$option->getId()]['value'][] = array(
                            'title' => $selection->getName(),
                            'qty' => $selectionQty->getValue(),
                            'price' => Mage::app()->getStore()->convertPrice($price)
                        );*/

                    }
                }
            }
        }

        $optionArr['bundle_options'] = $bundleOptions;

        /**
         * Product Prices calculations save
         */
        $optionArr['product_calculations'] = self::CALCULATE_PARENT;

        $optionArr['shipment_type'] = self::SHIPMENT_TOGETHER;

        return $optionArr;
    }

    /**
     * @param array $selectionIds
     * @param Mage_Catalog_Model_Product $product
     * @return Gri_Hamper_Model_Resource_Selection_Collection
     */
    public function getSelectionsByIds($selectionIds, $product = NULL)
    {
        $product = $this->getProduct($product);
        if (!$product->getBuyRequest()) {
            $buyRequest = new Varien_Object(unserialize($this->getProduct($product)->getCustomOption('info_buyRequest')->getValue()));
            $product->setBuyRequest($buyRequest);
        }
        $usedSelections = $product->getData($this->_keyUsedSelections);
        $usedSelectionsIds = $product->getData($this->_keyUsedSelectionsIds);
        $targetProducts = array_values($product->getBuyRequest()->getTargetProduct());
        if (!$usedSelections || serialize($usedSelectionsIds) != serialize($targetProducts)) {
            /* @var $usedSelections Gri_Hamper_Model_Resource_Selection_Collection */
            $usedSelections = Mage::getResourceModel('hamper/selection_collection');
            $usedSelections->setStore($product->getStore())
                ->addAttributeToSelect('*')
                ->addStoreFilter($this->getStoreFilter($product))
                ->addAttributeToFilter('entity_id', array('in' => $targetProducts))
                ->addAttributeToFilter('type_id', array('nin' => array('configurable', 'hamper', 'bundle', 'grouped')))
                ->setFlag('require_stock_items', TRUE);
            $product->setData($this->_keyUsedSelections, $usedSelections);
            $product->setData($this->_keyUsedSelectionsIds, $targetProducts);
        }
        return $usedSelections;
    }

    public function getSelectionsCollection($optionIds, $product = NULL)
    {
        $keyOptionIds = (is_array($optionIds) ? implode('_', $optionIds) : '');
        $key = $this->_keySelectionsCollection . $keyOptionIds;
        if (!$this->getProduct($product)->hasData($key)) {
            $storeId = $this->getProduct($product)->getStoreId();
            $selectionsCollection = Mage::getResourceModel('bundle/selection_collection')
                ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
                ->addAttributeToSelect('tax_class_id') //used for calculation item taxes in Bundle with Dynamic Price
                ->setFlag('require_stock_items', TRUE)
                ->setFlag('product_children', TRUE)
                ->setPositionOrder()
                ->addStoreFilter($this->getStoreFilter($product))
                ->setStoreId($storeId)
                ->setOptionIdsFilter($optionIds);

            if (!Mage::helper('catalog')->isPriceGlobal() && $storeId) {
                $websiteId = Mage::app()->getStore($storeId)->getWebsiteId();
                $selectionsCollection->joinPrices($websiteId);
            }

            $this->getProduct($product)->setData($key, $selectionsCollection);
        }
        return $this->getProduct($product)->getData($key);
    }

    public function getWeight($product = NULL)
    {
        if (!$product->getBuyRequest()) {
            $buyRequest = new Varien_Object(unserialize($this->getProduct($product)->getCustomOption('info_buyRequest')->getValue()));
            $product->setBuyRequest($buyRequest);
        }
        return parent::getWeight($product);
    }

    public function processBuyRequest($product, $buyRequest)
    {
        $options = parent::processBuyRequest($product, $buyRequest);
        $options['hamper_option'] = $buyRequest->getData('hamper_option');
        $options['hamper_message'] = $buyRequest->getData('hamper_message');
        return $options;
    }

    /**
     * Save type related data
     *
     * @param Mage_Catalog_Model_Product $product
     * @return Gri_Hamper_Model_Product_Type
     */
    public function save($product = NULL)
    {
        if (is_array($options = $product->getBundleOptionsData()) && $options) {
            $productId = $product->getId();
            $files = new Varien_Object();
            isset($_FILES['bundle_options']) and $files->setData($_FILES['bundle_options']);
            $imagePath = Mage::getBaseDir('media') . '/hamper/';
            is_dir($dir = $imagePath . '/' . $productId) or mkdir($dir, 0777, TRUE);
            foreach ($options as $k => $v) {
                if ($files->getData('error/' . $k . '/default_image') === 0) {
                    $defaultImageName = $productId . '/' . $files->getData('name/' . $k . '/default_image');
                    $options[$k]['default_image'] = $defaultImageName;
                    is_file($target = $imagePath . $defaultImageName) and unlink($target);
                    move_uploaded_file($files->getData('tmp_name/' . $k . '/default_image'), $target);
                }
                if ($files->getData('error/' . $k . '/image') === 0) {
                    $imageName = $productId . '/' . $files->getData('name/' . $k . '/image');
                    $options[$k]['image'] = $imageName;
                    is_file($target = $imagePath . $imageName) and unlink($target);
                    move_uploaded_file($files->getData('tmp_name/' . $k . '/image'), $target);
                }
            }
            $product->setBundleOptionsData($options);
        }
        parent::save($product);
        return $this;
    }
}
