<?php

/**
 * Helper functions
 *
 * @category   ProxiBlue
 * @package    DynCatProd
 * @author     Lucas van Staden (sales@proxiblue.com.au)
 */
class ProxiBlue_DynCatProd_Helper_Data extends Mage_Core_Helper_Abstract
{
    const CONFIG_PATH_VERTNAV_ROOTS = 'dyncatprod/vertnav/roots';
    protected $_vertNavRoots;

    protected function _restoreCurrentCategory($category)
    {
        if ($category) {
            $layer = Mage::getSingleton('catalog/layer');
            Mage::unregister('current_category');
            Mage::register('current_category', $category);
            $layer->setCurrentCategory($category);
        }
    }

    /**
     * Build the attribute map for admin selection.
     *
     * @return string
     */
    public function getAttributeMap() {
        $category = Mage::registry('category');
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('dyncatprod_');
        $renderer = Mage::getBlockSingleton('dyncatprod/adminhtml_system_config_form_field_attributes');
        $fieldset = $form->addFieldset('attributes_fieldset', array(
            'legend' => Mage::helper('dyncatprod')->__('Dynamically display any products that have the following attribute values set:')
        ));

        try {
            $dynamicAttributes = unserialize($category->getDynamicAttributes());
        } catch (Exception $e) {
            $dynamicAttributes = array();
        }
        if (is_array($dynamicAttributes)) {
            foreach ($dynamicAttributes as $key => $row) {
                if (!is_array($row)) {
                    unset($dynamicAttributes[$key]);
                }
            }
        }

        $fieldset->addField('attributes', 'text', array(
            'name' => 'dyncatprod_attributes',
            'title' => Mage::helper('dyncatprod')->__('Attributes'),
            'value' => $dynamicAttributes,
        ))->setRenderer($renderer);

        $html = $form->toHtml();
        return $html;
    }

    /**
     * @return array
     */
    public function getVertNavRoots()
    {
        if ($this->_vertNavRoots === NULL) {
            $roots = explode("\n", str_replace("\r", "\n", Mage::getStoreConfig(self::CONFIG_PATH_VERTNAV_ROOTS)));
            $this->_vertNavRoots = array_filter($roots);
        }
        return $this->_vertNavRoots;
    }

    /**
     * Attributes for select box in admin as an array
     *
     * @return array
     */
    public Function attributesOptionArray() {
        $attributes = Mage::getSingleton('catalog/config')->getProductAttributes();
        $eav_config = Mage::getModel('eav/config');
        $options = array();
        foreach ($attributes as $att_code) {
            $attribute = $eav_config->getAttribute('catalog_product', $att_code);

            $label = ($attribute->getFrontendLabel() != '') ? $attribute->getFrontendLabel() : $attribute->getAttributeCode();
            if ($attribute->getBackendType() == 'datetime') {
                if (strpos(strtolower($label), 'from') !== false) {
                    continue;
                }
                $label .= ' within date range';
                $label = str_replace('From', '', $label);
                $label = str_replace('To', '', $label);
                $label = str_replace('from', '', $label);
                $label = str_replace('to', '', $label);
            }
            $options[$attribute->getAttributeId()] = str_replace("'", "", $label);
        }
        $options['category_id'] = "Is in Category with Id(s)";
        asort($options);
        return $options;
    }

    /**
     * Build the dynamic products attributes to add to collection
     *
     * @param string $dynamicAttributes
     * @return array
     */
    public function buildDynamicProductCollection($dynamicAttributes) {
        try {
            $data = unserialize($dynamicAttributes);
            $filterList = array();
            $eav_config = Mage::getModel('eav/config');
            foreach ($data as $filter) {
                $type = 'eq';
                if (is_array($filter) && array_key_exists('attribute', $filter) && array_key_exists('value', $filter) && $filter['attribute'] != '0') {
                    $attribute = $eav_config->getAttribute('catalog_product', $filter['attribute']);
                    switch ($attribute->getFrontendInput()) {
                        case 'date':
                            $todayDate = Mage::app()->getLocale()->date()->toString(Varien_Date::DATE_INTERNAL_FORMAT);
                            // from
                            if (strpos($attribute->getAttributeCode(), 'to')) {
                                $attributeCode = str_replace('to', 'from', $attribute->getAttributeCode());
                            } else {
                                $attributeCode = $attribute->getAttributeCode();
                            }
                            $filterList[] = array('value' => array(
                                    'date' => true,
                                    'to' => $todayDate), 'attribute' => $attributeCode, 'join' => 'left', 'link' => 'AND');
                            // to
                            if (strpos($attribute->getAttributeCode(), 'from')) {
                                $attributeCode = str_replace('from', 'to', $attribute->getAttributeCode());
                            } else {
                                $attributeCode = $attribute->getAttributeCode();
                            }
                            $filterList[] = array('value' => array(
                                    'date' => true,
                                    'from' => $todayDate), 'attribute' => $attributeCode, 'join' => 'left', 'link' => 'AND');

                            continue;
                            break;
                        case 'price':
                            // do we have a range limiter?
                            $limiter = substr($filter['value'], 0, 2);
                            // is the right most numeric, and if so, we have a singular expression
                            $strip = 2;
                            $rightLimiter = substr($limiter, 1);
                            if (is_numeric($rightLimiter)) {
                                $limiter = substr($filter['value'], 0, 1);
                                $strip = 1;
                            }
                            if (!is_numeric($limiter)) {
                                // we have a range selector
                                $delimiterArray = array('>' => 'gt',
                                    '<' => 'lt',
                                    '!' => 'neq',
                                    '>=' => 'gteq',
                                    '<=' => 'lteq',
                                    '=' => 'eq'
                                );
                                if (!array_key_exists($limiter, $delimiterArray)) {
                                    Mage::log("Unknown Dynamic Price Limiter {$limiter} - reverted to '='");
                                    $limiter = '=';
                                }
                                $type = $delimiterArray[$limiter];
                                // remove the limitor
                                $filter['value'] = substr($filter['value'], $strip);
                            }
                            break;
                        case 'select':
                            $options = $attribute->getSource()->getAllOptions(true);
                            $filter['value'] = str_replace('*', '', $filter['value']);
                            foreach ($options as $option) {
                                if (empty($option['value'])) {
                                    continue;
                                }
                                if (empty($filter['value'])) {
                                    // match all
                                    $filterList[] = array('value' => array('finset' => array($option['value'])), 'attribute' => $attribute, 'join' => 'inner', 'link' => 'OR');
                                } else {
                                    try {
                                        if (substr_count($option['label'], $filter['value']) > 0) {
                                            $filterList[] = array('value' => array('finset' => array($option['value'])), 'attribute' => $attribute, 'join' => 'inner', 'link' => 'OR');
                                            //$filter['value'] = $option['value'];
                                            //break;
                                        }
                                    } catch (Exception $e) {
                                        $break = 1;
                                    }
                                }
                            }
                            break;
                        case 'multiselect':
                            $options = $attribute->getSource()->getAllOptions(true);
                            $filter['value'] = str_replace('*', '(\w+)', $filter['value']);
                            $filter['value'] = str_replace('/', '\/', $filter['value']);
                            foreach ($options as $option) {
                                if (preg_match('/' . $filter['value'] . '/i', $option['label'], $matches)) {
                                    $filterList[] = array('value' => array('finset' => array($option['value'])), 'attribute' => $attribute, 'join' => 'inner', 'link' => 'OR');
                                }
                            }
                            continue;
                            break;
                    }

                    if ($filter['value'] == '*') { // all
                        $type = 'like';
                        $filter['value'] = '%_%'; // mysql wildcard - all records
                    } else if (strpos($filter['value'], '*') !== false) {
                        $filter['value'] = str_replace('*', '%', $filter['value']); // wildcards
                        $type = 'like';
                    } else if (strtolower($filter['value']) == 'yes' || strtolower($filter['value']) == 'true') {
                        $filter['value'] = 1; //bool true
                    } else if (strtolower($filter['value']) == 'no' || strtolower($filter['value']) == 'false') {
                        $filter['value'] = 0; // bool false
                    }

                    if (!array_key_exists('link', $filter)) {
                        $filter['link'] = 'AND';
                    }
                    if (!empty($filter['value']) && $attribute->getFrontendInput() != 'date') {
                        $filterList[] = array('value' => array($type => $filter['value']), 'attribute' => $attribute, 'join' => 'inner', 'link' => $filter['link']);
                    }
                }
            }
            return $filterList;
        } catch (Exception $e) {
            Mage::logException($e);
            if (Mage::getIsDeveloperMode()) {
                die($e->getMessage());
            }
        }
    }

    /**
     * Add dynamic filters to a category collection.
     *
     * @param object $collection
     * @param integer $currentCategory
     * @return boolean
     */
    public function addDynamicFilters($collection, $currentCategory = false, $useIndex = true, $checkForCatFilter = false) {
        $layer = Mage::getSingleton('catalog/layer');
        $oldCurrentCategory = FALSE;
        if (!$currentCategory) {
            $currentCategory = Mage::registry('current_category');
        } else if (is_numeric($currentCategory) & !$checkForCatFilter) {
            $currentCategory = Mage::getModel('catalog/category')->load($currentCategory);
        } else if (!empty($_GET['cat']) && is_numeric($_GET['cat'])) {
            $currentCategory = Mage::getModel('catalog/category')->load((int) $_GET['cat']);
            $oldCurrentCategory = Mage::registry('current_category');
            Mage::unregister('current_category');
            Mage::register('current_category', $currentCategory, true); // override current category
            $layer->setCurrentCategory($currentCategory);
            $collection->setFlag('remove_cat_filter', $_GET['cat']);
        } else if (is_numeric($currentCategory)) {
            $currentCategory = Mage::getModel('catalog/category')->load($currentCategory);
            $oldCurrentCategory = Mage::registry('current_category');
            Mage::unregister('current_category');
            Mage::register('current_category', $currentCategory, true); // override current category
            $layer->setCurrentCategory($currentCategory);
        }
        if (is_object($currentCategory)) {
            if ($currentCategory->getDynamicAttributes() || strlen(trim($currentCategory->getDynamicAttributes())) > 0) {
                // check for indexed result, else do the long way
                // Prefer a direct sql query here, as it is faster
                if ($useIndex && Mage::getStoreConfig('dyncatprod/index/disabled') == 0) {
                    try {
                        $readAdapter = Mage::getSingleton('core/resource')->getConnection('core_read');
                        $select = $readAdapter->select()->from($collection->getTable('dyncatprod/catalog_category_dynamic_product_index'))
                                ->where("category_id= {$currentCategory->getId()} AND store_id = {$currentCategory->getStoreId()}");
                        $indexedData = $readAdapter->fetchAll($select);
                        if (is_array($indexedData) && count($indexedData) > 0) {
                            $data = array_shift($indexedData);
                            $collection->addFieldToFilter('entity_id', array('in' => array(explode(',', $data['product_ids']))));
                            $collection->getSelect()->distinct(true);
                            $collection->setFlag('is_dynamic', true);
                            $this->_restoreCurrentCategory($oldCurrentCategory);
                            return true;
                        }
                    } catch (Exception $e) {
                        // there was an issue, so go the non indexed way.
                        Mage::logException($e);
                    }
                }
                $filterList = $this->buildDynamicProductCollection($currentCategory->getDynamicAttributes());
                if (count($filterList) > 0) {
                    $orFilter = array();
                    foreach ($filterList as $key => $filter) {
                        try {
                            if (is_object($filter['attribute']) && $filter['attribute']->getAttributeCode() == 'category_id') {
                                if (Mage::app()->getStore()->isAdmin()) {
                                    //is there a + at the right side, if so include all child categories
                                    $setCategories = explode(',', $filter['value']['eq']);
                                    $rebuiltCategories = array();
                                    $subCats = array();
                                    foreach ($setCategories as $categoryId) {
                                        $rightMost = substr($categoryId, -1, 1);
                                        if ($rightMost == '+') {
                                            // get all child category ids.
                                            $categoryId = substr($categoryId, 0, strlen($categoryId) - 1);
                                            $categoryModel = Mage::getModel('catalog/category')->load($categoryId);
                                            if ($categoryModel) {
                                                $subCats = explode(',', $categoryModel->getChildren());
                                            }
                                        }
                                        $rebuiltCategories = array_merge($rebuiltCategories, $subCats, array($categoryId));
                                    }
                                    $filter['value']['eq'] = implode(',', array_unique(array_filter($rebuiltCategories)));
                                    $conditions = array(
                                        'cat_index.product_id=e.entity_id',
                                        $collection->getConnection()->quoteInto('cat_index.category_id IN (' . $filter['value']['eq'] . ')', "")
                                    );
                                    $joinCond = join(' AND ', $conditions);
                                    $fromPart = $collection->getSelect()->getPart(Zend_Db_Select::FROM);

                                    if (isset($fromPart['cat_index'])) {
                                        $fromPart['cat_index']['joinCondition'] = $joinCond;
                                        $collection->getSelect()->setPart(Zend_Db_Select::FROM, $fromPart);
                                    } else {
                                        $collection->getSelect()->join(
                                                array('cat_index' => $collection->getTable('catalog/category_product_index')), $joinCond, array('cp_category_id' => 'category_id')
                                        );
                                    }
                                } else {
                                    $collection->setFlag('category_ids', $filter['value']['eq']);
                                    $collection->setFlag('store_id', $currentCategory->getStoreId());
                                }
                                continue;
                            }
                            if ($filter['link'] == 'OR') {
                                $orFilter[] = array('attribute' => $filter['attribute']->getAttributeCode(), key($filter['value']) => $filter['value']);
                                //Mage::log("cat_add:".$collection->getSelect());
                            } else {
                                $collection->addAttributeToFilter($filter['attribute'], $filter['value'], $filter['join']);
                            }
                        } catch (Exception $e) {
                            Mage::logException($e);
                        }
                    }

                    if (count($orFilter) > 0) {
                        $collection->addAttributeToFilter($orFilter, null, 'left');
                        //Mage::log("after:".$collection->getSelect());
                    }
                    $collection->getSelect()->distinct(true);
                    $collection->getSelect()->group('e.entity_id');
                    $collection->setFlag('is_dynamic', true);
                }
                //Mage::log("after:" . $collection->getSelect());
                $this->_restoreCurrentCategory($oldCurrentCategory);
                return $collection;
            }
        }
        $this->_restoreCurrentCategory($oldCurrentCategory);
        return false;
    }

    public function removeCatFilter($collection) {
        $fromPart = $collection->getSelect()->getPart(Zend_Db_Select::FROM);
        if (isset($fromPart['cat_index']) && isset($fromPart['cat_index']['joinCondition'])) {
            $joinParts = explode('AND', $fromPart['cat_index']['joinCondition']);
            foreach ($joinParts as $key => $part) {
                if (strPos($part, $collection->getFlag('remove_cat_filter')) > 0) {
                    unset($joinParts[$key]);
                }
            }
            $fromPart['cat_index']['joinCondition'] = implode(' AND ', $joinParts);
            $collection->getSelect()->setPart(Zend_Db_Select::FROM, $fromPart);
            $collection->getSelect()->group('e.entity_id');
        }
        return $collection;
    }

    public function removeIndexTables($collection) {
        $select = $collection->getSelect();
        $fromPart = $select->getPart(Zend_Db_Select::FROM);
        $select->reset(Zend_Db_Select::FROM);
//        if(array_key_exists('price_index', $fromPart)){
//            unset($fromPart['price_index']);
//        }
        if (array_key_exists('cat_index', $fromPart)) {
            unset($fromPart['cat_index']);
        }
        $select->setPart(Zend_Db_Select::FROM, $fromPart);
        return $collection;
    }

}
