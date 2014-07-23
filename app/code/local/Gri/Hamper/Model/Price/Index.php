<?php

/**
 * Class Gri_Hamper_Model_Price_Index
 * @method Gri_Hamper_Model_Resource_Price_Index _getResource()
 */
class Gri_Hamper_Model_Price_Index extends Mage_Bundle_Model_Price_Index
{
    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('hamper/price_index');
    }

    /**
     * Add bundle price range index to Product collection
     *
     * @param Mage_Catalog_Model_Resource_Product_Collection $collection
     * @return Mage_Bundle_Model_Price_Index
     */
    public function addPriceIndexToCollection($collection)
    {
        $productObjects = array();
        $productIds = array();
        foreach ($collection->getItems() as $product) {
            /* @var $product Mage_Catalog_Model_Product */
            if ($product->getTypeId() == Gri_Hamper_Model_Product_Type::TYPE_HAMPER) {
                $productIds[] = $product->getEntityId();
                $productObjects[$product->getEntityId()] = $product;
            }
        }
        $websiteId = Mage::app()->getStore($collection->getStoreId())
            ->getWebsiteId();
        $groupId = Mage::getSingleton('customer/session')
            ->getCustomerGroupId();

        $addOptionsToResult = FALSE;
        $prices = $this->_getResource()->loadPriceIndex($productIds, $websiteId, $groupId);
        foreach ($productIds as $productId) {
            if (isset($prices[$productId])) {
                $productObjects[$productId]
                    ->setData('_price_index', TRUE)
                    ->setData('_price_index_min_price', $prices[$productId]['min_price'])
                    ->setData('_price_index_max_price', $prices[$productId]['max_price']);
            } else {
                $addOptionsToResult = TRUE;
            }
        }

        if ($addOptionsToResult) {
            $collection->addOptionsToResult();
        }

        return $this;
    }
}
