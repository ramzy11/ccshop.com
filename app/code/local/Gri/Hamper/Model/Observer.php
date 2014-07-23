<?php

class Gri_Hamper_Model_Observer extends Mage_Bundle_Model_Observer
{

    /**
     * Append hamper in upsell list for current product
     *
     * @param Varien_Event_Observer $observer
     * @return Gri_Hamper_Model_Observer
     */
    public function appendUpsellProducts($observer)
    {
        /* @var $product Mage_Catalog_Model_Product */
        $product = $observer->getEvent()->getProduct();

        /**
         * Check is current product type is allowed for bundle selection product type
         */
        if (!in_array($product->getTypeId(), Mage::helper('hamper')->getAllowedSelectionTypes())) {
            return $this;
        }

        /* @var $collection Mage_Catalog_Model_Resource_Product_Link_Product_Collection */
        $collection = $observer->getEvent()->getCollection();
        $limit = $observer->getEvent()->getLimit();
        if (is_array($limit)) {
            if (isset($limit['upsell'])) {
                $limit = $limit['upsell'];
            } else {
                $limit = 0;
            }
        }

        /* @var $resource Mage_Bundle_Model_Resource_Selection */
        $resource = Mage::getResourceSingleton('bundle/selection');

        $productIds = array_keys($collection->getItems());
        if (!is_null($limit) && $limit <= count($productIds)) {
            return $this;
        }

        // retrieve bundle product ids
        $bundleIds = $resource->getParentIdsByChild($product->getId());
        // exclude up-sell product ids
        $bundleIds = array_diff($bundleIds, $productIds);

        if (!$bundleIds) {
            return $this;
        }

        /* @var $bundleCollection Mage_Catalog_Model_Resource_Product_Collection */
        $bundleCollection = $product->getCollection()
            ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
            ->addStoreFilter()
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents();

        Mage::getSingleton('catalog/product_visibility')
            ->addVisibleInCatalogFilterToCollection($bundleCollection);

        if (!is_null($limit)) {
            $bundleCollection->setPageSize($limit);
        }
        $bundleCollection->addFieldToFilter('entity_id', array('in' => $bundleIds))
            ->setFlag('do_not_use_category_id', TRUE);

        if ($collection instanceof Varien_Data_Collection) {
            foreach ($bundleCollection as $item) {
                $collection->addItem($item);
            }
        } elseif ($collection instanceof Varien_Object) {
            $items = $collection->getItems();
            foreach ($bundleCollection as $item) {
                $items[$item->getEntityId()] = $item;
            }
            $collection->setItems($items);
        }

        return $this;
    }

    /**
     * Append selection attributes to selection's order item
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Bundle_Model_Observer
     */
    public function appendHamperSelectionData($observer)
    {
        return parent::appendBundleSelectionData($observer);
    }

    /**
     * duplicating bundle options and selections
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Bundle_Model_Observer
     */
    public function duplicateProduct($observer)
    {
        $product = $observer->getEvent()->getCurrentProduct();

        if ($product->getTypeId() != Gri_Hamper_Model_Product_Type::TYPE_HAMPER) {
            //do nothing if not hamper
            return $this;
        }

        $newProduct = $observer->getEvent()->getNewProduct();

        $product->getTypeInstance(TRUE)->setStoreFilter($product->getStoreId(), $product);
        $optionCollection = $product->getTypeInstance(TRUE)->getOptionsCollection($product);
        $selectionCollection = $product->getTypeInstance(TRUE)->getSelectionsCollection(
            $product->getTypeInstance(TRUE)->getOptionsIds($product),
            $product
        );
        $optionCollection->appendSelections($selectionCollection);

        $optionRawData = array();
        $selectionRawData = array();

        $i = 0;
        foreach ($optionCollection as $option) {
            $optionRawData[$i] = array(
                'required' => $option->getData('required'),
                'position' => $option->getData('position'),
                'type' => $option->getData('type'),
                'title' => $option->getData('title') ? $option->getData('title') : $option->getData('default_title'),
                'delete' => ''
            );
            foreach ($option->getSelections() as $selection) {
                $selectionRawData[$i][] = array(
                    'product_id' => $selection->getProductId(),
                    'position' => $selection->getPosition(),
                    'is_default' => $selection->getIsDefault(),
                    'selection_price_type' => $selection->getSelectionPriceType(),
                    'selection_price_value' => $selection->getSelectionPriceValue(),
                    'selection_qty' => $selection->getSelectionQty(),
                    'selection_can_change_qty' => $selection->getSelectionCanChangeQty(),
                    'delete' => ''
                );
            }
            $i++;
        }

        $newProduct->setBundleOptionsData($optionRawData);
        $newProduct->setBundleSelectionsData($selectionRawData);
        return $this;
    }

    /**
     * Setting attribute tab block for hamper
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Bundle_Model_Observer
     */
    public function setAttributeTabBlock($observer)
    {
        $product = $observer->getEvent()->getProduct();
        if ($product->getTypeId() == Gri_Hamper_Model_Product_Type::TYPE_HAMPER) {
            Mage::helper('adminhtml/catalog')
                ->setAttributeTabBlock('hamper/adminhtml_catalog_product_edit_tab_attributes');
        }
        return $this;
    }

    public function updateBundleProductOptions(Varien_Event_Observer $observer)
    {
        /* @var $product Mage_Catalog_Model_Product */
        $product = $observer->getEvent()->getSelection();
        $responseObject = $observer->getEvent()->getResponseObject();
        $additionalOptions = array('product' => $product->getId());
        $responseObject->setAdditionalOptions($additionalOptions);
    }

    /**
     * Initialize product options renderer with hamper specific params
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Bundle_Model_Observer
     */
    public function initOptionRenderer(Varien_Event_Observer $observer)
    {
        $block = $observer->getBlock();
        $block->addOptionsRenderCfg('hamper', 'hamper/catalog_product_configuration');
        return $this;
    }
}
