<?php

/**
 * 
 *
 * @category   ProxiBlue
 * @package    DynCatProd
 * @author     Lucas van Staden (sales@proxiblue.com.au)
 */

class ProxiBlue_DynCatProd_Model_Enterprise_Search_Catalog_Layer extends Enterprise_Search_Model_Catalog_Layer
{
    /**
     * Retrieve current layer product collection
     *
     * @return Enterprise_Search_Model_Resource_Collection
     */
    public function getProductCollection()
    {
        if (isset($this->_productCollections[$this->getCurrentCategory()->getId()])) {
            return $this->_productCollections[$this->getCurrentCategory()->getId()];
        }    
        if ($this->getCurrentCategory()->getDynamicAttributes() && trim($this->getCurrentCategory()->getDynamicAttributes()) != '' && !is_null($this->getCurrentCategory()->getDynamicAttributes())) {
            // bypass enterprise solr layer collection, else filters don't work
            $collection = Mage::getSingleton('catalog/layer')->getProductCollection();
            $this->_productCollections[$this->getCurrentCategory()->getId()] = $collection;
            return $collection;
        } 
        return parent::getProductCollection();
        
        
    }

}
