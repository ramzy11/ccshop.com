<?php

class Gri_Hamper_Block_View extends Mage_Bundle_Block_Catalog_Product_View_Type_Bundle
{

    protected function _construct()
    {
        parent::_construct();
        if ($this->getData('parent_block')) {
            /* @var $parent Mage_Catalog_Block_Product_View */
            $this->setParentBlock($parent = $this->getData('parent_block'));
            foreach ($parent->getChild() as $alias => $block) {
                $this->setChild($alias, $block);
            }
        }
        if (!$this->getTemplate()) {
            /* @var $type Gri_Hamper_Model_Product_Type */
            $type = $this->getProduct()->getTypeInstance();
            $template = $type->getIsPreset() ? 'hamper/view/preset.phtml' : 'hamper/view/custom.phtml';
            $this->setTemplate($template);
        }
        return $this;
    }

    public function getHamperItems()
    {
        $product = $this->getProduct();
        if ($product->getHamperItems() === NULL) {
            /* @var $type Gri_Hamper_Model_Product_Type */
            $type = $product->getTypeInstance();
            $children = $type->getChildrenIds($product->getId(), FALSE);
            foreach ($children as $g => $group) {
                foreach ($group as $k => $v) {
                    /* @var $subProduct Mage_Catalog_Model_Product */
                    $subProduct = Mage::getModel('catalog/product');
                    $children[$g][$k] = $subProduct->load($v);
                }
            }
            $product->setHamperItems($children);
        }
        return $product->getHamperItems();
    }

    public function getItemCount()
    {
        $count = 0;
        foreach ($this->getHamperItems() as $items) $count += count($items);
        return $count;
    }

    public function getJsonConfig()
    {
        /* @var $coreHelper Mage_Core_Helper_Data */
        $coreHelper = Mage::helper('core');
        $product = $this->getProduct();

        /* Start Parent getJsonConfig() */
        $options = array();
        $selected = array();
        $multiTypes = array('multi' => 1, 'checkbox' => 1);
        /* @var $taxHelper Mage_Tax_Helper_Data */
        $taxHelper = Mage::helper('tax');
        /* @var $catalogHelper Mage_Catalog_Helper_Data */
        $catalogHelper = Mage::helper('catalog');

        foreach ($this->getOptions() as $_option) {
            /* @var $_option Mage_Bundle_Model_Option */
            if (!$optionSelections = $_option->getSelections()) continue;

            $optionId = $_option->getId();
            $option = array(
                'selections' => array(),
                'title' => $_option->getTitle(),
                'isMulti' => isset($multiTypes[$_option->getType()]),
            );

            $selectionCount = count($optionSelections);

            foreach ($optionSelections as $_selection) {
                /* @var $_selection Mage_Catalog_Model_Product */
                $selectionId = $_selection->getSelectionId();

                $selection = array(
                    'qty' => 1,
                    'customQty' => $_selection->getSelectionCanChangeQty(),
                    'price' => 0,
                    'priceInclTax' => 0,
                    'priceExclTax' => 0,
                    'priceValue' => $coreHelper->currency($_selection->getSelectionPriceValue(), FALSE, FALSE),
                    'priceType' => $_selection->getSelectionPriceType(),
                    'tierPrice' => array(),
                    'name' => $_selection->getName(),
                    'plusDisposition' => 0,
                    'minusDisposition' => 0,
                    'canApplyMAP' => FALSE,
                );

                $responseObject = new Varien_Object();
                $args = array('response_object' => $responseObject, 'selection' => $_selection);
                Mage::dispatchEvent('bundle_product_view_config', $args);
                if (is_array($responseObject->getAdditionalOptions())) foreach ($responseObject->getAdditionalOptions() as $o => $v) {
                    $selection[$o] = $v;
                }
                $responseObject->unsetData();
                unset($responseObject);
                $option['selections'][$selectionId] = $selection;

                if (($_selection->getIsDefault() || ($selectionCount == 1 && $_option->getRequired()))
                    && $_selection->isSalable()
                ) {
                    $selected[$optionId][] = $selectionId;
                }
            }
            $options[$optionId] = $option;
        }

        $config = array(
            'options' => $options,
            'selected' => $selected,
            'bundleId' => $product->getId(),
            'priceFormat' => Mage::app()->getLocale()->getJsPriceFormat(),
            'basePrice' => $coreHelper->currency($product->getPrice(), FALSE, FALSE),
            'priceType' => $product->getPriceType(),
            'specialPrice' => $product->getSpecialPrice(),
            'includeTax' => $taxHelper->priceIncludesTax() ? 'true' : 'false',
            'isFixedPrice' => $product->getPriceType() == Mage_Bundle_Model_Product_Price::PRICE_TYPE_FIXED,
            'isMAPAppliedDirectly' => $catalogHelper->canApplyMsrp($product, NULL, FALSE)
        );

        /* End Parent getJsonConfig() */

        /* @var $priceModel Gri_Hamper_Model_Product_Price */
        $priceModel = $product->getPriceModel();
        $availableSelection = array();
        foreach ($config['options'] as $option) {
            $availableSelection += $option['selections'];
        }
        $preConfiguredValues = $product->getPreconfiguredValues();
        $config = array_merge($config, array(
            'discount' => $priceModel->parseDiscount($product),
            'gifts' => $priceModel->parseGifts($product),
            'availableSelection' => $availableSelection,
            'defaultValues' => $preConfiguredValues->getData('hamper_option'),
        ));
        return $coreHelper->jsonEncode($config);
    }

    public function getOptions()
    {
        if (!$this->_options) {
            /* @var $productHelper Mage_Catalog_Helper_Product */
            $productHelper = Mage::helper('catalog/product');
            $productHelper->setSkipSaleableCheck(TRUE);
            parent::getOptions();
            $productHelper->setSkipSaleableCheck(FALSE);
        }
        return $this->_options;
    }

    /**
     * @param Mage_Bundle_Model_Option $option
     * @return string
     */
    public function getOptionHeader($option)
    {
        $html = '';
        $img = $option->getDefaultImage();
        $option->getImage() and $img = $option->getImage();
        $title = $option->getDefaultTitle();
        $option->getTitle() and $title = $option->getTitle();
        if ($img) {
            $html .= sprintf('<img src="%s" title="%s"/>', Mage::getBaseUrl('media') . '/hamper/' . $img, $title);
        } else $html .= $title;
        return $html;
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @return $this
     */
    public function prepareProductUrl($product)
    {
        $product->setUrl('');
        $product->setShopNowUrl('javascript:;');
        return $this;
    }
}
