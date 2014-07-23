<?php
class Gri_CatalogCustom_Block_Adminhtml_Catalogcustom_Editorpick_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('productGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(TRUE);
        $this->setUseAjax(TRUE);
        $this->setVarNameFilter('product_filter');
    }

    protected function _getStore()
    {
        $storeId = (int)$this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }

    protected function _prepareCollection()
    {
        $store = $this->_getStore();
        $visibility = array(
            Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
            Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG
        );
        /* @var $collection Mage_Catalog_Model_Resource_Product_Collection */
        $collection = Mage::getModel('catalog/product')->getCollection();
        $collection->addAttributeToSelect('name')
            ->joinAttribute(
                'editors_pick',
                'catalog_product/editors_pick',
                'entity_id',
                NULL,
                'left'
            )
            ->addAttributeToFilter('visibility', $visibility);

        if ($store->getId()) {
            //$collection->setStoreId($store->getId());
            $adminStore = Mage_Core_Model_App::ADMIN_STORE_ID;
            $collection->addStoreFilter($store);
            $collection->joinAttribute(
                'name',
                'catalog_product/name',
                'entity_id',
                NULL,
                'inner',
                $adminStore
            );

            $collection->joinAttribute(
                'status',
                'catalog_product/status',
                'entity_id',
                NULL,
                'inner',
                $store->getId()
            );
        } else {
            $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', NULL, 'inner');
        }

        $this->setCollection($collection);

        parent::_prepareCollection();
        $this->getCollection()->addWebsiteNamesToResult();
        return $this;
    }

    protected function _addColumnFilterToCollection($column)
    {
        if ($this->getCollection()) {
            if ($column->getId() == 'websites') {
                $this->getCollection()->joinField(
                    'websites',
                    'catalog/product_website',
                    'website_id',
                    'product_id=entity_id',
                    NULL,
                    'left'
                );
            }
        }
        return parent::_addColumnFilterToCollection($column);
    }

    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header' => Mage::helper('gri_catalogcustom')->__('ID'),
            'type' => 'number',
            'index' => 'entity_id',
        ));
        $this->addColumn('name', array(
            'header' => Mage::helper('gri_catalogcustom')->__('Name'),
            'index' => 'name',
        ));

        $this->addColumn('type', array(
            'header' => Mage::helper('gri_catalogcustom')->__('Type'),
            'index' => 'type_id',
            'type' => 'options',
            'options' => Mage::getSingleton('catalog/product_type')->getOptionArray(),
        ));
        $this->addColumn('sku', array(
            'header' => Mage::helper('gri_catalogcustom')->__('SKU'),
            'index' => 'sku',
        ));

        $this->addColumn('status', array(
            'header' => Mage::helper('gri_catalogcustom')->__('Status'),
            'index' => 'status',
            'type' => 'options',
            'options' => Mage::getSingleton('catalog/product_status')->getOptionArray(),
        ));
        $brand_attribute_id = Mage::getModel('eav/entity_attribute')->getIdByCode('catalog_product', 'brand');
        $brands = Mage::getResourceModel('eav/entity_attribute_option_collection')
            ->setAttributeFilter($brand_attribute_id)
            ->setStoreFilter()
            ->load()
            ->toOptionHash();
        $this->addColumn('brand', array(
            'header' => Mage::helper('gri_catalogcustom')->__('Brand'),
            'index' => 'brand',
            'type' => 'options',
            'options' => $brands,
        ));
        $this->addColumn('editors_pick', array(
            'header' => Mage::helper('gri_catalogcustom')->__('Editor\'s Pick'),
            'index' => 'editors_pick',
            'type' => 'number',
        ));

        $this->addColumn('action', array(
            'header' => Mage::helper('gri_catalogcustom')->__('Action'),
            'width' => '50px',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(array(
                'caption' => Mage::helper('gri_catalogcustom')->__('Edit'),
                'url' => array(
                    'base' => '*/*/edit',
                    'params' => array('store' => $this->getRequest()->getParam('store'))
                ),
                'field' => 'id'
            )),
            'filter' => FALSE,
            'sortable' => FALSE,
            'index' => 'stores',
        ));

        if (Mage::helper('gri_catalogcustom')->isModuleEnabled('Mage_Rss')) {
            $this->addRssList('rss/catalog/notifystock', Mage::helper('gri_catalogcustom')->__('Notify Low Stock RSS'));
        }

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('product');
        if (Mage::getSingleton('admin/session')->isAllowed('catalog/update_attributes')) {
            $this->getMassactionBlock()->addItem('attributes', array(
                'label' => Mage::helper('catalog')->__('Update Attributes'),
                'url' => $this->getUrl('*/catalogcustom_product_action_editorpick/edit', array('_current' => TRUE))
            ));
        }

        Mage::dispatchEvent('adminhtml_catalog_product_grid_prepare_massaction', array('block' => $this));
        return $this;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => TRUE));
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array(
            'store' => $this->getRequest()->getParam('store'),
            'id' => $row->getId()
        ));
    }
}
