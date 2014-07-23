<?php
/**
 * @category    Mana
 * @package     Mana_Filters
 * @copyright   Copyright (c) http://www.manadev.com
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
/**
 * Block type for showing options for filter based on custom attribute
 * @author Mana Team
 * Injected into layout instead of standard catalog/layer_filter_attribute in Mana_Filters_Block_View_Category::_initBlocks.
 */
class Mana_Filters_Block_FilterProductType extends Mage_Core_Block_Template {

    public function getSelectedSeoValues()
    {
        $result = array();
        foreach ($this->getItems() as $item) {
            /* @var $item Mana_Filters_Model_Item */
            if ($item->getMSelected()) {
                $result[] = $item->getSeoValue();
            }
        }
        return implode('_', $result);
    }

    /**
     * Get clear all selected filter item url
     *
     */
    public function getRemoveUrl()
    {
    	$query = array(strtolower($this->getFilterOptions()->getCode()) =>null);
    	$params['_current']     = true;
    	$params['_use_rewrite'] = true;
    	$params['_query']       = $query;
    	$params['_escape']      = true;
    	return Mage::getUrl('*/*/*', $params);
    }

    public function getAttributeSetIdOptions()
    {
        /* @var $_flashSale Gri_FlashSale_Model_FlashSale */
        $_flashSale = Mage::helper('gri_flashsale')->getActiveFlashSale();
        /* @var $collection Gri_FlashSale_Model_Resource_Product_Collection */
        $collection = $_flashSale->getAssociatedProductsWithoutAttributeSetId();

        $attributeSetIds = $collection->getSetIds();
        $attributeSetIdOptions = array();
        foreach($attributeSetIds as $setId){
            $set = Mage::getModel('eav/entity_attribute_set')
                ->load($setId);
            $attributeSetIdOptions[$setId] = $set->getAttributeSetName();
        }

        return $attributeSetIdOptions;
    }
}
