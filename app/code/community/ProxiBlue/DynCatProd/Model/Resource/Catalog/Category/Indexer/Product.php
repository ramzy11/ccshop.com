<?php

class ProxiBlue_DynCatProd_Model_Resource_Catalog_Category_Indexer_Product extends Mage_Index_Model_Resource_Abstract {

    /**
     * Category table
     *
     * @var string
     */
    protected $_categoryTable;

    /**
     * Category product table
     *
     * @var string
     */
    protected $_categoryProductTable;

    /**
     * Product website table
     *
     * @var string
     */
    protected $_productWebsiteTable;

    /**
     * Store table
     *
     * @var string
     */
    protected $_storeTable;

    /**
     * Group table
     *
     * @var string
     */
    protected $_groupTable;

    /**
     * Array of info about stores
     *
     * @var array
     */
    protected $_storesInfo;
    protected $_categoryModel;

    /**
     * Model initialization
     *
     */
    protected function _construct() {
        $this->_init('dyncatprod/catalog_category_dynamic_product_index', 'category_id');
        $this->_categoryTable = $this->getTable('catalog/category');
        $this->_categoryProductTable = $this->getTable('catalog/category_product');
        $this->_productWebsiteTable = $this->getTable('catalog/product_website');
        $this->_storeTable = $this->getTable('core/store');
        $this->_groupTable = $this->getTable('core/store_group');
    }

    /**
     * Process product save.
     * Method is responsible for index support
     * when product was saved and assigned categories was changed.
     *
     * @param Mage_Index_Model_Event $event
     * @return Mage_Catalog_Model_Resource_Category_Indexer_Product
     */
    public function catalogProductSave(Mage_Index_Model_Event $event) {
        return $this;
    }

    /**
     * Process Catalog Product mass action
     *
     * @param Mage_Index_Model_Event $event
     * @return Mage_Catalog_Model_Resource_Category_Indexer_Product
     */
    public function catalogProductMassAction(Mage_Index_Model_Event $event) {
        return $this;
    }

    /**
     * Return array of used root category id - path pairs
     *
     * @return array
     */
    protected function _getRootCategories() {
        $rootCategories = array();
        $stores = $this->_getStoresInfo();
        foreach ($stores as $storeInfo) {
            if ($storeInfo['root_id']) {
                $rootCategories[$storeInfo['root_id']] = $storeInfo['root_path'];
            }
        }
        return $rootCategories;
    }

    /**
     * Process category index after category save
     *
     * @param Mage_Index_Model_Event $event
     */
    public function catalogCategorySave(Mage_Index_Model_Event $event) {
        $data = $event->getNewData();
        /**
         * Check if the dynamic products data has changed
         */
        if (isset($data['dynamic_products_was_changed'])) {
            try {
                $this->beginTransaction();
                $this->clearTemporaryIndexTable();
                $idxTable = $this->getIdxTable();
                $idxAdapter = $this->_getIndexAdapter();
                $stores = $this->_getStoresInfo();
                foreach ($stores as $storeData) {
                    $storeId = $storeData['store_id'];
                    $currentCategory = $data['affected_category'];
                    $this->buildTempIndex($currentCategory, $storeId, $idxAdapter, $idxTable);
                }
                $this->commit();
                $this->insertFromTable($idxTable,$this->getMainTable());
                $this->clearTemporaryIndexTable();
                
            } catch (Exception $e) {
                $this->rollBack();
                throw $e;
            }
            return $this;
        }
    }

    /**
     * Rebuild all index data
     *
     * @return Mage_Catalog_Model_Resource_Category_Indexer_Product
     */
    public function reindexAll() {
        $this->useIdxTable(true);
        $this->beginTransaction();
        try {
            $this->clearTemporaryIndexTable();
            $idxTable = $this->getIdxTable();
            $idxAdapter = $this->_getIndexAdapter();
            $stores = $this->_getStoresInfo();
            /**
             * Build index for each store
             */
            foreach ($stores as $storeData) {
                $storeId = $storeData['store_id'];
                $rootId = $storeData['root_id'];

                if (is_null($this->_categoryModel)) {
                    $this->_categoryModel = Mage::getModel('catalog/category');
                }
                $treeModel = $this->_categoryModel->getTreeModel()->loadNode($rootId);

                $nodes = $treeModel->loadChildren()->getAllChildNodes();

                $nodeIds = array();
                foreach ($nodes as $node) {
                    $nodeIds[] = $node->getId();
                }

                $collection = $this->_categoryModel->getCollection()
                        ->addAttributeToSelect('dynamic_attributes')
                        ->addAttributeToFilter('is_active', 1)
                        ->addIdFilter($nodeIds)
                        ->addAttributeToSort('entity_id')
                        ->load();

                foreach ($collection as $currentCategory) {
                    $this->buildTempIndex($currentCategory, $storeId, $idxAdapter, $idxTable);
                }
            }

            $this->syncData();

            /**
             * Clean up temporary tables
             */
            $this->clearTemporaryIndexTable();
            $this->commit();
        } catch (Exception $e) {
            $this->rollBack();
            throw $e;
        }
        return $this;
    }

    protected function buildTempIndex($currentCategory, $storeId, $idxAdapter, $idxTable) {
        $productCollection = Mage::getResourceModel('catalog/product_collection')->setStoreId($storeId);
        if (Mage::helper('dyncatprod')->addDynamicFilters($productCollection, $currentCategory, false)) {
            $productCollection->load();
            $ids = $productCollection->getAllIds();
            $ids = array_unique($ids);
            if(count($ids) > 0){
                try {
                    $idxAdapter->insert($idxTable, array('category_id' => $currentCategory->getId(), 'product_ids' => implode(',', $ids), 'store_id' => $storeId));
                } catch (Exception $e) {
                    Mage::logException($e);
                    Mage::log('Could not insert data for dynamic category index using data: Category: ' .  print_r($currentCategory,true) . ' and ids of ' . implode(',', $ids));
                }
            }
            // save this into the temp index table
        }
    }

    /**
     * Get array with store|website|root_categry path information
     *
     * @return array
     */
    protected function _getStoresInfo() {
        if (is_null($this->_storesInfo)) {
            $adapter = $this->_getReadAdapter();
            $select = $adapter->select()
                    ->from(array('s' => $this->getTable('core/store')), array('store_id', 'website_id'))
                    ->join(
                            array('sg' => $this->getTable('core/store_group')), 'sg.group_id = s.group_id', array())
                    ->join(
                    array('c' => $this->getTable('catalog/category')), 'c.entity_id = sg.root_category_id', array(
                'root_path' => 'path',
                'root_id' => 'entity_id'
                    )
            );
            $this->_storesInfo = $adapter->fetchAll($select);
        }

        return $this->_storesInfo;
    }

    /**
     * Retrieve temporary decimal index table name
     *
     * @param string $table
     * @return string
     */
    public function getIdxTable($table = null) {
        return $this->getTable('dyncatprod/catalog_category_dynamic_product_indexer_idx');
    }

}
