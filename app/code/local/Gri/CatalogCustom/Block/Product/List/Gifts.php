<?php

class Gri_CatalogCustom_Block_Product_List_Gifts extends Gri_CatalogCustom_Block_Category_Group
{
    protected $_giftCardCollection;
    protected $_giftProductCollection;

    protected function _construct()
    {
        parent::_construct();
        $this->getData('column_count') or $this->setData('column_count', 4);
        $this->getTemplate() or $this->setTemplate('catalog/category/group.phtml');
        $this->getProductHelper()->getProductListItemBlock()->setShowPrice(FALSE)
            ->setShowRewardPoints(TRUE)
            ->setShopNowLabel('REDEEM NOW');
        $this->setCategories(array(
            Mage::getModel('catalog/category', array(
                'entity_id' => 'gift-cards',
                'name' => $this->__('Gift Cards'),
                'product_collection' => $this->_getGiftCardCollection(),
            )),
            Mage::getModel('catalog/category', array(
                'entity_id' => 'gift-products',
                'name' => $this->__('Gift Products'),
                'product_collection' => $this->_getGiftProductCollection(),
            )),
        ));
    }

    /**
     * Retrieve Catalog Config object
     *
     * @return Mage_Catalog_Model_Config
     */
    protected function _getConfig()
    {
        return Mage::getSingleton('catalog/config');
    }

    protected function _getGiftCardCollection()
    {
        if ($this->_giftCardCollection === NULL) {
            /* @var $collection Mage_Catalog_Model_Resource_Product_Collection */
            $collection = Mage::getResourceModel('catalog/product_collection');
            /* @var $layer Mage_Catalog_Model_Layer */
            $layer = Mage::getModel('catalog/layer');
            $layer->prepareProductCollection($collection);
            $collection->joinAttribute('reward_points', 'catalog_product/reward_points', 'entity_id', NULL, 'left')
                ->addAttributeToFilter('type_id', 'giftcard')
                ->addStoreFilter();

            $this->_giftCardCollection = $collection;
        }
        return $this->_giftCardCollection;
    }

    protected function _getGiftProductCollection()
    {
        if ($this->_giftProductCollection === NULL) {
            /* @var $collection Mage_Catalog_Model_Resource_Product_Collection */
            $collection = Mage::getResourceModel('catalog/product_collection');
            /* @var $layer Mage_Catalog_Model_Layer */
            $layer = Mage::getModel('catalog/layer');
            $layer->prepareProductCollection($collection);
            $collection->joinAttribute('reward_points', 'catalog_product/reward_points', 'entity_id', NULL, 'left')
                ->addAttributeToFilter('attribute_set_id', $this->_getConfig()->getAttributeSetId('catalog_product', 'Gifts'))
                ->addStoreFilter();

            $this->_giftProductCollection = $collection;
        }
        return $this->_giftProductCollection;
    }

    /**
     * Get Product Helper
     * @return Gri_CatalogCustom_Helper_Product
     */
    public function getProductHelper()
    {
        return Mage::helper('gri_catalogcustom/product');
    }
}
