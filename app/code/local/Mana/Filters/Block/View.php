<?php
/**
 * @category    Mana
 * @package     Mana_Filters
 * @copyright   Copyright (c) http://www.manadev.com
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
/**
 * Block type for showing filters in category view pages.
 * @author Mana Team
 * Injected into layout instead of standard catalog/layer_view in layout XML file.
 */
class Mana_Filters_Block_View extends Mage_Catalog_Block_Layer_View
{
    protected $_mode = 'category';

    /**
     * This method is called during page rendering to generate additional child blocks for this block.
     * @return Mana_Filters_Block_View_Category
     * This method is overridden by copying (method body was pasted from parent class and modified as needed). All
     * changes are marked with comments.
     * @see app/code/core/Mage/Catalog/Block/Layer/Mage_Catalog_Block_Layer_View::_prepareLayout()
     */
    protected function _prepareLayout()
    {
        $stateBlock = $this->getLayout()->createBlock('mana_filters/state')
            ->setLayer($this->getLayer());
        $this->setChild('layer_state', $stateBlock);

        foreach ($this->getManaHelper()->getFilterOptionsCollection() as $filterOptions) {
            $displayOptions = $filterOptions->getDisplayOptions();
            $block = $this->getLayout()->createBlock((string)$displayOptions->block, '', array(
                'filter_options' => $filterOptions,
                'display_options' => $displayOptions,
            ))->setLayer($this->getLayer());
            if ($attribute = $filterOptions->getAttribute()) {
                $block->setAttributeModel($attribute);
            }
            $block->setMode($this->_mode)->init();
            $this->setChild($filterOptions->getCode() . '_filter', $block);
        }

        $this->getLayer()->apply();

        return $this;
    }

    public function canShowBrandFilter()
    {
        if ($this->getData('can_show_brand_filter') === NULL) {
            $result = FALSE;
            ($category = Mage::registry('current_category')) && !$category->getBrandCategory() and
                $result = TRUE;
            $this->setData('can_show_brand_filter', $result);
        }
        return $this->getData('can_show_brand_filter');
    }

    public function getClearUrl()
    {
        return $this->getManaHelper()->getClearUrl();
    }

    public function getFilters()
    {
        if ($this->getData('filters') === NULL) {
            $collection = $this->getManaHelper()->getFilterOptionsCollection();
            $filters = array_flip($this->showFilters());
            foreach ($collection as $filterOptions) {
                if (!isset($filters[$filterOptions->getCode()])) continue;
                if ($filterOptions->getIsEnabled()) {
                    $filters[$filterOptions->getCode()] = $this->getChild($filterOptions->getCode() . '_filter');
                }
            }
            foreach ($filters as $k => $v) if (!is_object($v)) unset($filters[$k]);
            $this->setData('filters', $filters);
        }
        return $this->getData('filters');
    }

    /**
     * @return Mana_Filters_Helper_Data
     */
    public function getManaHelper()
    {
        return $this->helper('mana_filters');
    }

    public function showFilters()
    {
        $filters = array();
        if ($this->canShowBrandFilter()) $filters[] = 'brand';
        $filters = array_merge($filters, array('price', 'color_filter_1'));
        if (!in_array($this->getRequest()->getRouteName(), array('catalogsearch', 'gri_catalogsearch'))) {
            $filters[] = 'size_filter_1';
            $filters[] = 'heel_height';
            $filters[] = 'size_shoes';
            $filters[] = 'size_clothing';
        }
        return $filters;
    }

    public function getSeoValues()
    {
        $selectedSeoValues = array();
        /* @var $_filter Mana_Filters_Block_Filter */
        foreach($this->getFilters() as $_filter) {
            /* @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
            $attribute = $_filter->getFilter()->getAttributeModel();
            /* @var $item Mana_Filters_Model_Item */
            foreach($_filter->getItems() as $item) {
                $item->getMSelected() && $item->getCount() >= 1 && $selectedSeoValues[$attribute->getAttributeCode()][] = $item->getValue();
            }
        }

        return $selectedSeoValues;
    }
}
