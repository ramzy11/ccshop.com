<?php

/**
 * Catelog Category functions
 *
 * @category   ProxiBlue
 * @package    DynCatProd
 * @author     Lucas van Staden (sales@proxiblue.com.au)
 */

class ProxiBlue_DynCatProd_Model_Category extends Gri_CatalogCustom_Model_Category {

    /**
     * Get category products collection
     *
     * @return Varien_Data_Collection_Db
     */
    public function getProductCollection() {
        $collection = Mage::getResourceModel('catalog/product_collection')->setStoreId($this->getStoreId());
        if (Mage::helper('dyncatprod')->addDynamicFilters($collection,$this->getEntityId(),true,true)){
            return $collection;
        }
        return parent::getProductCollection();
    }

    /**
     * Set the category product count, taking dynamic products into consideration
     *
     * @param type $value
     */

    public function setProductCount($value){
        // get the dynamic products count, and add it.
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('sku');
            //->addStoreFilter($this->getRequest()->getParam('store'))
        $count = 0;

        if (Mage::helper('dyncatprod')->addDynamicFilters($collection,$this->getId())){
            $collection->load();
            $count = $collection->count();
            $value = 0;
        }
        $this->setData('product_count',$value + $count);
    }

    /**
     * Retrieve count products of category
     *
     * @return int
     */
    public function getProductCount()
    {
        $this->setProductCount($this->getData('product_count'));
        if (!$this->hasProductCount()) {
            $count = $this->_getResource()->getProductCount($this); // load product count
            $this->setData('product_count', $count);
        }
        return $this->getData('product_count');
    }
}
