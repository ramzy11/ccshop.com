<?php

class ProxiBlue_DynCatProd_Model_Resource_Product extends Mage_Catalog_Model_Resource_Product
{
    
    /**
     * Retrieve product category identifiers
     * 
     * Adjusted to include dynamic assigned categories
     *
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    public function getCategoryIds($product)
    {
        $result = parent::getCategoryIds($product);
        // add dynamic categories
        $table = $this->getTable('dyncatprod/catalog_category_dynamic_product_index');
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->distinct(true)
            ->from($table, 'category_id')
            ->where('FIND_IN_SET (?, product_ids)', (int)$product->getId())
            ->where('store_id = ?', (int)Mage::app()->getStore()->getStoreId());
 
        $resultDynamic = $adapter->fetchCol($select);
        
        return array_merge($result,$resultDynamic);
        
    }
    
    /**
     * Get collection of product categories
     * 
     * Adjusted to include dynamic assigned categories
     *
     * @param Mage_Catalog_Model_Product $product
     * @return Mage_Catalog_Model_Resource_Category_Collection
     */
    public function getCategoryCollection($product)
    {
        $collection = Mage::getResourceModel('catalog/category_collection')
            ->joinField('product_id',
                'catalog/category_product',
                'product_id',
                'category_id = entity_id',
                null,'left')    
            ->addFieldToFilter('product_id', (int)$product->getId());
        
        $select = $collection->getSelect();
        
        $adapter = $this->_getReadAdapter();
        $table = $this->getTable('dyncatprod/catalog_category_dynamic_product_index');
        $subSelect = $adapter->select()
            ->distinct(true)
            ->from($table, 'category_id')
            ->where('FIND_IN_SET (?, product_ids)', (int)$product->getId())
            ->where('store_id = ?', (int)Mage::app()->getStore()->getStoreId());
        
        $select->orWhere('e.entity_id IN (?)', new Zend_Db_Expr($subSelect->__toString()));
        
        return $collection;
    }
    
    /**
     * Retrieve category ids where product is available
     * 
     * Adjusted to take dynamic categories into consideration
     *
     * @param Mage_Catalog_Model_Product $object
     * @return array
     */
    public function getAvailableInCategories($object)
    {
        $result = parent::getAvailableInCategories($object);
        // add dynamic categories

        $table = $this->getTable('dyncatprod/catalog_category_dynamic_product_index');
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->distinct(true)
            ->from($table, 'category_id')
            ->where('FIND_IN_SET (?, product_ids)', (int)$object->getId())
            ->where('store_id = ?', (int)Mage::app()->getStore()->getStoreId());
 
        $resultDynamic = $adapter->fetchCol($select);
        
        return array_merge($result,$resultDynamic);
        
    }
    
    /**
     * Check availability display product in category
     * 
     * Adjusted to check dynamic categoris index, if normal index returns a false
     *
     * @param Mage_Catalog_Model_Product $product
     * @param int $categoryId
     * @return string
     */
    public function canBeShowInCategory($product, $categoryId)
    {
        $result = parent::canBeShowInCategory($product, $categoryId);
        if($result){
            return $result;
        }
        $table = $this->getTable('dyncatprod/catalog_category_dynamic_product_index');
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->distinct(true)
            ->from($table, 'category_id')
            ->where('FIND_IN_SET (?, product_ids)', (int)$product->getId())
            ->where('category_id = ?', (int)$categoryId)
            ->where('store_id = ?', (int)Mage::app()->getStore()->getStoreId());
        return $this->_getReadAdapter()->fetchOne($select);
        
        
    }

}
