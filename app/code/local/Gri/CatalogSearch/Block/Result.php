<?php
class Gri_CatalogSearch_Block_Result extends Mage_CatalogSearch_Block_Result
{
 	public function getPromotedIterms()
    {
    	if (!$this->getData('promoted_terms')) {
    		if($iterms = $this->_getQuery()->getData('promoted_terms')) {
    			$this->setData('promoted_terms',explode(',',$iterms));
    		}
    	}
    	return $this->getData('promoted_terms');
    }

    public function getPagerUrl($params=array())
    {
    	$urlParams = array();
    	$urlParams['_current']  = true;
    	$urlParams['_escape']   = true;
    	$urlParams['_use_rewrite']   = true;
    	$urlParams['_query']    = $params;
    	return $this->getUrl('*/*/*', $urlParams);
    }

    public function getSearchItemUrl($item)
    {
		return $this->getPagerUrl(array(Mage_CatalogSearch_Helper_Data::QUERY_VAR_NAME => $item));
    }

}