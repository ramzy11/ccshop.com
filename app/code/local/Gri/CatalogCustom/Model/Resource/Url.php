<?php

class Gri_CatalogCustom_Model_Resource_Url extends Mage_Catalog_Model_Resource_Url
{

    protected function _getProducts($productIds, $storeId, $entityId, &$lastEntityId)
    {
        $statusAttribute = $this->getProductModel()->getResource()->getAttribute('status');
        $archivedAttribute = $this->getProductModel()->getResource()->getAttribute('is_archived');
        $visibilityAttribute = $this->getProductModel()->getResource()->getAttribute('visibility');
        $products   = array();
        $websiteId  = Mage::app()->getStore($storeId)->getWebsiteId();
        $adapter    = $this->_getReadAdapter();
        if ($productIds !== null) {
            if (!is_array($productIds)) {
                $productIds = array($productIds);
            }
        }
        $bind = array(
            'website_id' => (int)$websiteId,
            'entity_id'  => (int)$entityId,
        );
        $select = $adapter->select()
            ->useStraightJoin(true)
            ->from(array('e' => $this->getTable('catalog/product')), array('entity_id'))
            ->join(
                array('w' => $this->getTable('catalog/product_website')),
                'e.entity_id = w.product_id AND w.website_id = :website_id',
                array()
            )
            ->join(
                array('s' => $statusAttribute->getBackendTable()),
                $adapter->quoteInto('s.entity_id = e.entity_id AND s.store_id = 0 AND s.attribute_id = ? AND s.value = 1', $statusAttribute->getAttributeId()),
                array()
            )
            ->join(
                array('a' => $archivedAttribute->getBackendTable()),
                $adapter->quoteInto('a.entity_id = e.entity_id AND a.store_id = 0 AND a.attribute_id = ? AND a.value = 0', $archivedAttribute->getAttributeId()),
                array()
            )
            ->join(
                array('v' => $visibilityAttribute->getBackendTable()),
                $adapter->quoteInto('v.entity_id = e.entity_id AND v.store_id = 0 AND v.attribute_id = ? AND v.value > 1', $visibilityAttribute->getAttributeId()),
                array()
            )
            ->where('e.entity_id > :entity_id')
            ->order('e.entity_id')
            ->limit($this->_productLimit);
        if ($productIds !== null) {
            $select->where('e.entity_id IN(?)', $productIds);
        }

        $rowSet = $adapter->fetchAll($select, $bind);
        foreach ($rowSet as $row) {
            $product = new Varien_Object($row);
            $product->setIdFieldName('entity_id');
            $product->setCategoryIds(array());
            $product->setStoreId($storeId);
            $products[$product->getId()] = $product;
            $lastEntityId = $product->getId();
        }

        unset($rowSet);

        if ($products) {
            $select = $adapter->select()
                ->from(
                    $this->getTable('catalog/category_product'),
                    array('product_id', 'category_id')
                )
                ->where('product_id IN(?)', array_keys($products));
            $categories = $adapter->fetchAll($select);
            foreach ($categories as $category) {
                $productId = $category['product_id'];
                $categoryIds = $products[$productId]->getCategoryIds();
                $categoryIds[] = $category['category_id'];
                $products[$productId]->setCategoryIds($categoryIds);
            }

            foreach (array('name', 'url_key', 'url_path') as $attributeCode) {
                $attributes = $this->_getProductAttribute($attributeCode, array_keys($products), $storeId);
                foreach ($attributes as $productId => $attributeValue) {
                    $products[$productId]->setData($attributeCode, $attributeValue);
                }
            }
        }

        return $products;
    }
}
