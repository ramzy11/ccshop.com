<?php

class Gri_Hamper_Block_Adminhtml_Catalog_Product_Edit_Tab_Hamper_Option_Search_Grid extends Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Bundle_Option_Search_Grid
{

    /**
     * @return Gri_Hamper_Helper_Data
     */
    protected function _getHelper()
    {
        return $this->helper('hamper');
    }

    /**
     * @param Mage_Catalog_Model_Resource_Product_Collection $collection
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     * @return $this
     */
    protected function _filterEntityCondition($collection, $column)
    {
        if ($column->getId() == 'id' && $value = $column->getFilter()->getValue()) {
            $collection->addIdFilter($value);
        }
        return $this;
    }

    protected function _prepareCollection()
    {
        /* @var $collection Mage_Catalog_Model_Resource_Product_Collection */
        $collection = Mage::getModel('catalog/product')->getCollection();
        $collection->setStore($this->getStore())
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('price')
            ->addAttributeToSelect('attribute_set_id')
            ->addAttributeToFilter('type_id', array('in' => $this->getAllowedSelectionTypes()))
            ->addStoreFilter();

        if ($products = $this->_getProducts()) {
            $collection->addIdFilter($this->_getProducts(), true);
        }

        if ($this->getFirstShow()) {
            $collection->addIdFilter('-1');
            $this->setEmptyText($this->__('Please enter search conditions to view products.'));
        }

        Mage::getSingleton('catalog/product_status')->addSaleableFilterToCollection($collection);

        $this->setCollection($collection);

        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        parent::_prepareColumns();
        $this->addColumnAfter('type', array(
            'header' => Mage::helper('catalog')->__('Type'),
            'width' => '60px',
            'index' => 'type_id',
            'type' => 'options',
            'options' => Mage::getSingleton('catalog/product_type')->getOptionArray(),
        ), 'set_name')->removeColumn('qty');
        $this->getColumn('id')->setData('filter_condition_callback', array($this, '_filterEntityCondition'));
        return Mage_Adminhtml_Block_Widget_Grid::_prepareColumns();
    }

    public function getAllowedSelectionTypes()
    {
        return $this->_getHelper()->getAllowedSelectionTypes();
    }

    public function getGridUrl()
    {
        return $this->getUrl('gri_hamper/selection/grid', array('index' => $this->getIndex(), 'productss' => implode(',', $this->_getProducts())));
    }
}
