<?php

/**
 * 
 *
 * @category   ProxiBlue
 * @package    DynCatProd
 * @author     Lucas van Staden (sales@proxiblue.com.au)
 */

class ProxiBlue_DynCatProd_Model_Resource_Enterprise_Search_Engine extends Enterprise_Search_Model_Resource_Engine
{
    
    /**
     * Retrieve search engine adapter model by adapter name
     * Now supporting only Solr search engine adapter
     *
     * @param string $adapterName
     * @return object
     */
    protected function _getAdapterModel($adapterName)
    {
        $model = '';
        switch ($adapterName) {
            case 'solr':
            default:
                if (extension_loaded('solr')) {
                    $model = 'enterprise_search/adapter_phpExtension';
                } else {
                    $model = 'dyncatprod/enterprise_search_adapter_httpStream';
                }
                break;
        }

        return Mage::getSingleton($model);
    }

    
}
