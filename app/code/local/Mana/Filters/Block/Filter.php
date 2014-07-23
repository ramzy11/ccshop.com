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
class Mana_Filters_Block_Filter extends Mage_Catalog_Block_Layer_Filter_Abstract {

    /**
     * This function is typically called to initialize underlying model of filter and apply it to current
     * product set if needed. Here we leave it as is except that we assign template file here not in constructor,
     * not how standard Magento does.
     * @see Mage_Catalog_Block_Layer_Filter_Abstract::init()
     */
    public function init() {
    	$this->setTemplate((string)$this->getDisplayOptions()->template);
        $this->_filterModelName = (string)$this->getDisplayOptions()->model;
    	return parent::init();
    }

    protected function _prepareFilter() {
    	if ($this->getAttributeModel()) {
        	$this->_filter->setAttributeModel($this->getAttributeModel());
    	}
    	$this->_filter
    		->setFilterOptions($this->getFilterOptions())
    		->setDisplayOptions($this->getDisplayOptions())
    		->setMode($this->getMode());
    	return $this;
    }

    /**
     * Returns underlying model object which contains actual filter data
     * @return Mage_Catalog_Model_Layer_Filter_Attribute
     */

    public function getFilter() {
    	return $this->_filter;
    }

    public function getName() {
    	return $this->getFilterOptions()->getName();
    }

    public function getSelectedSeoValues() {
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



    //for price slider
    public function getPriceFiterHtml()
    {
        $minPrice = Mage::getSingleton('mana_filters/filter_price')->getLowestPossibleValue();
        $maxPrice = Mage::getSingleton('mana_filters/filter_price')->getHighestPossibleValue();
        $step = 10;
        $url = Mage::helper('core/url')->getCurrentUrl();

        $params = Mage::app()->getRequest()->getParams();
        if(isset($params['price']))
        {
            $price = explode('-',$params['price']);
            $curMinPrice = $price[0];
            $curMaxPrice = $price[1];
        }else{
            $curMinPrice = $minPrice;
            $curMaxPrice = $maxPrice;
        }

        $html = '<div id="price-rangeslider"></div>';
        $html .= '<div id="price-rangeslider-label"><input type="text" id="price-rangeslider-min" /><input type="text" id="price-rangeslider-max" /></div>';
        $html .= '<input type="hidden" id="price-rangeslider-values"
                      cur_min_price="'.$curMinPrice.'"
                      cur_max_price="'.$curMaxPrice.'"
                      min_price="'.$minPrice.'"
                      max_price="'.$maxPrice.'"
                      step = "'.$step.'"
                      currency_symbol = "'.$this->getCurrencySymbol().'"
                      url="'.$url.'">';

       return $html;
    }

    /*
	* Fetch Current Currency symbol
	*
	* @return currency
	*/
    public function getCurrencySymbol(){
        return Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
    }

}