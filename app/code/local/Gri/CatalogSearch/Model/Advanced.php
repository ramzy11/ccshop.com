<?php

/**
 * Catalog advanced search model
 *
 * @method Mage_CatalogSearch_Model_Resource_Advanced _getResource()
 * @method Mage_CatalogSearch_Model_Resource_Advanced getResource()
 * @method int getEntityTypeId()
 * @method Mage_CatalogSearch_Model_Advanced setEntityTypeId(int $value)
 * @method int getAttributeSetId()
 * @method Mage_CatalogSearch_Model_Advanced setAttributeSetId(int $value)
 * @method string getTypeId()
 * @method Mage_CatalogSearch_Model_Advanced setTypeId(string $value)
 * @method string getSku()
 * @method Mage_CatalogSearch_Model_Advanced setSku(string $value)
 * @method int getHasOptions()
 * @method Mage_CatalogSearch_Model_Advanced setHasOptions(int $value)
 * @method int getRequiredOptions()
 * @method Mage_CatalogSearch_Model_Advanced setRequiredOptions(int $value)
 * @method string getCreatedAt()
 * @method Mage_CatalogSearch_Model_Advanced setCreatedAt(string $value)
 * @method string getUpdatedAt()
 * @method Mage_CatalogSearch_Model_Advanced setUpdatedAt(string $value)
 *
 * @category    Gri
 * @package     Gri_CatalogSearch
 */
class Gri_CatalogSearch_Model_Advanced extends Mage_CatalogSearch_Model_Advanced
{
    /**
     * Add advanced search filters to product collection
     *
     * @param   array $values
     * @return  Mage_CatalogSearch_Model_Advanced
     */
    public function addFilters($values)
    {
        $attributes     = $this->getAttributes();
        $hasConditions  = false;
        $allConditions  = array();

        if( isset($values['category_id']) && $categoryId = intval($values['category_id'])) {
            $category = Mage::getModel('catalog/category')->load($categoryId);
            $category->getId() && $this->getProductCollection()->addCategoryFilter($category);
            unset($values['category_id']);
        }

        if(isset($values['name']) && Mage::helper('gri_catalogsearch/advanced')->getEscapedQueryText() == $values['name']){
            unset($values['name']);
        }

        foreach ($attributes as $attribute) {
            /* @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
            if (!isset($values[$attribute->getAttributeCode()])) {
                continue;
            }
            $value = $values[$attribute->getAttributeCode()];

            if ($attribute->getAttributeCode() == 'price') {
                $value['from'] = isset($value['from']) ? trim($value['from']) : '';
                $value['to'] = isset($value['to']) ? trim($value['to']) : '';
                if (is_numeric($value['from']) || is_numeric($value['to'])) {
                    if (!empty($value['currency'])) {
                        $rate = Mage::app()->getStore()->getBaseCurrency()->getRate($value['currency']);
                    } else {
                        $rate = 1;
                    }
                    if ($this->_getResource()->addRatedPriceFilter(
                        $this->getProductCollection(), $attribute, $value, $rate)
                    ) {
                        $hasConditions = true;
                        $this->_addSearchCriteria($attribute, $value);
                    }
                }
            } else if ($attribute->isIndexable()) {
                if (!is_string($value) || strlen($value) != 0) {
                    if ($this->_getResource()->addIndexableAttributeModifiedFilter(
                        $this->getProductCollection(), $attribute, $value)) {
                        $hasConditions = true;
                        $this->_addSearchCriteria($attribute, $value);
                    }
                }
            } else {
                $condition = $this->_prepareCondition($attribute, $value);
                Mage::log('condition='.var_export($condition,true).' AttributeCode='.$attribute->getAttributeCode().' value='.$value, 7, 'advanced.log');
                if ($condition === false) {
                    continue;
                }

                $this->_addSearchCriteria($attribute, $value);

                $table = $attribute->getBackend()->getTable();
                if ($attribute->getBackendType() == 'static'){
                    $attributeId = $attribute->getAttributeCode();
                } else {
                    $attributeId = $attribute->getId();
                }
                $allConditions[$table][$attributeId] = $condition;
            }
        }

        if ($allConditions) {
            $this->getProductCollection()->addFieldsToFilter($allConditions);
        } else if (!$hasConditions) {
            Mage::throwException(Mage::helper('catalogsearch')->__('Please specify at least one search term.'));
        }

        return $this;
    }

    public function renderCollectionByAttributeCode($productCollection, $params = array())
    {

    }
}
