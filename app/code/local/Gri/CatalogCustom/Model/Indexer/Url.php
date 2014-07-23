<?php

class Gri_CatalogCustom_Model_Indexer_Url extends Mage_Catalog_Model_Indexer_Url
{

    protected function _registerProductEvent(Mage_Index_Model_Event $event)
    {
        $product = $event->getDataObject();
        $dataChange = $product->dataHasChangedFor('url_key')
            || $product->dataHasChangedFor('status')
            || $product->dataHasChangedFor('is_archived')
            || $product->dataHasChangedFor('visibility')
            || $product->getIsChangedCategories()
            || $product->getIsChangedWebsites();

        if (!$product->getExcludeUrlRewrite() && $dataChange) {
            $event->addNewData('rewrite_product_ids', array($product->getId()));
        }
    }
}
