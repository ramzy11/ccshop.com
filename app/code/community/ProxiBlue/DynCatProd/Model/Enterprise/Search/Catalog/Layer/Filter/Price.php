<?php

/**
 * 
 *
 * @category   ProxiBlue
 * @package    DynCatProd
 * @author     Lucas van Staden (sales@proxiblue.com.au)
 */

class ProxiBlue_DynCatProd_Model_Enterprise_Search_Catalog_Layer_Filter_Price extends Enterprise_Search_Model_Catalog_Layer_Filter_Price
{
    

    /**
     * Add params to faceted search
     *
     * @return Enterprise_Search_Model_Catalog_Layer_Filter_Category
     */
    public function addFacetCondition()
    {
        $collection = $this->getLayer()->getProductCollection();
        if($collection instanceof Enterprise_Search_Model_Resource_Collection) {
            return parent::addFacetCondition();
        }
        return $this;
    }

    
}
