<?php

class Gri_CatalogCustom_Block_Home_MostRecentViews extends Mage_Core_Block_Template
{
    protected $_collection = NULL;

    protected $_template = 'catalog/home/most_recent_views.phtml';

    /**
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function getMostRecentViews()
    {
        if($this->_collection == NULL) {
            $sql = " SELECT `rvpi`.`product_id` FROM `report_viewed_product_index` rvpi
                     INNER JOIN `catalog_product_entity` cpe ON `rvpi`.`product_id` = `cpe`.`entity_id`
                     WHERE `cpe`.`type_id`='configurable' ORDER BY `rvpi`.`index_id` DESC limit 20";

            $productIds = Mage::getSingleton('core/resource')
                                   ->getConnection('core_read')
                                   ->fetchCol($sql);

            $this->_collection = Mage::getModel('catalog/product')->getCollection()
                                   ->addAttributeToSelect('*')
                                   ->addPriceData()
                                   ->addStoreFilter()
                                   ->addIdFilter($productIds)
                                   ->setPage(1, 10);

          //  Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($this->_collection);
          //  Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($this->_collection);
        }

        return$this->_collection;
    }
}
