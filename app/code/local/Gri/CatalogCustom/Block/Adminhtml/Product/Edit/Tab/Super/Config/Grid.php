<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml super product links grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Gri_CatalogCustom_Block_Adminhtml_Product_Edit_Tab_Super_Config_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
   public function __construct()
    {
        parent::__construct();
        $this->setUseAjax(true);
        $this->setId('super_product_links');

        if ($this->_getProduct()->getId()) {
            $this->setDefaultFilter(array('in_products'=>1));
        }
    }

    /**
     * Retrieve currently edited product object
     *
     * @return Mage_Catalog_Model_Product
     */
    protected function _getProduct()
    {
        return Mage::registry('current_product');
    }

    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in product flag
        if ($column->getId() == 'in_products') {
            $productIds = $this->_getSelectedProducts();

            if (empty($productIds)) {
                $productIds = 0;
            }

            $createdProducts = $this->_getCreatedProducts();

            $existsProducts = $productIds; // Only for "Yes" Filter we will add created products

            if(count($createdProducts)>0) {
                if(!is_array($existsProducts)) {
                    $existsProducts = $createdProducts;
                } else {
                    $existsProducts = array_merge($createdProducts);
                }
            }

            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in'=>$existsProducts));
            }
            else {
                if($productIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', array('nin'=>$productIds));
                }
            }
        }
        else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    protected function _getCreatedProducts()
    {
        $products = $this->getRequest()->getPost('new_products', null);
        if (!is_array($products)) {
            $products = array();
        }

        return $products;
    }

    protected function _prepareCollection()
    {
	
        $allowProductTypes = array();
        foreach (Mage::helper('catalog/product_configuration')->getConfigurableAllowedTypes() as $type) {
            $allowProductTypes[] = $type->getName();
        }

        $product = $this->_getProduct();
        $collection = $product->getCollection()
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('attribute_set_id')
			->addAttributeToSelect('ref_no')
            ->addAttributeToSelect('type_id')
            ->addAttributeToSelect('price')
            ->addFieldToFilter('attribute_set_id',$product->getAttributeSetId())
            ->addFieldToFilter('type_id', $allowProductTypes)
            ->addFilterByRequiredOptions()
			
            ->joinAttribute('name', 'catalog_product/name', 'entity_id', null, 'inner');

        if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
            //Mage::getModel('cataloginventory/stock_item')->addCatalogInventoryToProductCollection($collection);
			$collection->getSelect()->joinLeft(array('cisi'=>'cataloginventory_stock_item'),'cisi.product_id = e.entity_id',array('cisi.is_in_stock', 'cisi.qty'));
        }
		
        foreach ($product->getTypeInstance(true)->getUsedProductAttributes($product) as $attribute) {
            $collection->addAttributeToSelect($attribute->getAttributeCode());
            $collection->addAttributeToFilter($attribute->getAttributeCode(), array('notnull'=>1));
        }


        $this->setCollection($collection);

        if ($this->isReadonly()) {
            $collection->addFieldToFilter('entity_id', array('in' => $this->_getSelectedProducts()));
        }
		
		parent::_prepareCollection();
		return $this;
    }

    protected function _getSelectedProducts()
    {
        $products = $this->getRequest()->getPost('products', null);
        if (!is_array($products)) {
            $products = $this->_getProduct()->getTypeInstance(true)->getUsedProductIds($this->_getProduct());
        }
        return $products;
    }

    /**
     * Check block is readonly
     *
     * @return boolean
     */
    public function isReadonly()
    {
        if ($this->hasData('is_readonly')) {
            return $this->getData('is_readonly');
        }
        return $this->_getProduct()->getCompositeReadonly();
    }
	
    protected function _prepareColumns()
    {
        $product = $this->_getProduct();
        $attributes = $product->getTypeInstance(true)->getConfigurableAttributes($product);

        if (!$this->isReadonly()) {
            $this->addColumn('in_products', array(
                'header_css_class' => 'a-center',
                'type'      => 'checkbox',
                'name'      => 'in_products',
                'values'    => $this->_getSelectedProducts(),
                'align'     => 'center',
                'index'     => 'entity_id',
                'renderer'  => 'adminhtml/catalog_product_edit_tab_super_config_grid_renderer_checkbox',
                'attributes' => $attributes
            ));
        }
	
        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('catalog')->__('ID'),
            'sortable'  => true,
            'width'     => '60px',
            'index'     => 'entity_id'
        ));
        $this->addColumn('name', array(
            'header'    => Mage::helper('catalog')->__('Name'),
            'index'     => 'name'
        ));

		$this->addColumn('ref_no',array(
			'header'	=> Mage::helper('catalog')->__('Ref. No'),
			'index'		=> 'ref_no',
		));

        $sets = Mage::getModel('eav/entity_attribute_set')->getCollection()
            ->setEntityTypeFilter($this->_getProduct()->getResource()->getTypeId())
            ->load()
            ->toOptionHash();

        $this->addColumn('set_name',
            array(
                'header'=> Mage::helper('catalog')->__('Attrib. Set Name'),
                'width' => '130px',
                'index' => 'attribute_set_id',
                'type'  => 'options',
                'options' => $sets,
        ));
		
        $this->addColumn('sku', array(
            'header'    => Mage::helper('catalog')->__('SKU'),
            'width'     => '80px',
            'index'     => 'sku'
        ));

        $this->addColumn('price', array(
            'header'    => Mage::helper('catalog')->__('Price'),
            'type'      => 'currency',
            'currency_code' => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
            'index'     => 'price'
        ));


		
        $this->addColumn('is_saleable', array(
            'header'    => Mage::helper('catalog')->__('Inventory'),
            'renderer'  => 'adminhtml/catalog_product_edit_tab_super_config_grid_renderer_inventory',
            'filter'    => 'adminhtml/catalog_product_edit_tab_super_config_grid_filter_inventory',
            'index'     => 'is_saleable'
        ));
		
		$this->addColumn('qty',array(
			'header' => Mage::helper('catalog')->__('Qty'),
			'type' => 'number',
			'index'=> 'qty',
			'filter_condition_callback'=>array($this,'_qtyFilter'),
			'order_callback'=>array($this, '_qtyOrder'),
		));
		
        foreach ($attributes as $attribute) {

            $productAttribute = $attribute->getProductAttribute();
            $productAttribute->getSource();
            $this->addColumn($productAttribute->getAttributeCode(), array(
                'header'    => $productAttribute->getFrontend()->getLabel(),
                'index'     => $productAttribute->getAttributeCode(),
                'type'      => $productAttribute->getSourceModel() ? 'options' : 'number',
                'options'   => $productAttribute->getSourceModel() ? $this->getOptions($attribute) : ''
            ));
        }

         $this->addColumn('action',
            array(
                'header'    => Mage::helper('catalog')->__('Action'),
                'type'      => 'action',
                'getter'     => 'getId',
                'actions'   => array(
                    array(
                        'caption' => Mage::helper('catalog')->__('Edit'),
                        'url'     => $this->getEditParamsForAssociated(),
                        'field'   => 'id',
                        'onclick'  => 'superProduct.createPopup(this.href);return false;'
                    )
                ),
                'filter'    => false,
                'sortable'  => false
        ));

        return parent::_prepareColumns();
    }

    public function getEditParamsForAssociated()
    {
        return array(
            'base'      =>  '*/*/edit',
            'params'    =>  array(
                'required' => $this->_getRequiredAttributesIds(),
                'popup'    => 1,
                'product'  => $this->_getProduct()->getId()
            )
        );
    }

    /**
     * Retrieve Required attributes Ids (comma separated)
     *
     * @return string
     */
    protected function _getRequiredAttributesIds()
    {
        $attributesIds = array();
        foreach (
            $this->_getProduct()
                ->getTypeInstance(true)
                ->getConfigurableAttributes($this->_getProduct()) as $attribute
        ) {
            $attributesIds[] = $attribute->getProductAttribute()->getId();
        }

        return implode(',', $attributesIds);
    }

    public function getOptions($attribute) {
        $result = array();
        foreach ($attribute->getProductAttribute()->getSource()->getAllOptions() as $option) {
            if($option['value']!='') {
                $result[$option['value']] = $option['label'];
            }
        }

        return $result;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/superConfig', array('_current'=>true));
    }
	
	public function _qtyFilter($collection, $column)
	{
		if(!$value = $column->getFilter()->getValue())
		{
			return $this;
		}
		
		$from = $value['from'];
		$to = $value['to'];
		
		if(!is_null($from))
		{
			$this->getCollection()->getSelect()->where('cisi.qty >= ?',$from);
		}
		
		if(!is_null($to))
		{
			$this->getCollection()->getSelect()->where('cisi.qty <= ?',$to);
		}
		
		return $this;
	}
	
	public function _qtyOrder($collection, $column)
	{
		$this->getCollection()->getSelect()->order('cisi.qty '. strtoupper($column->getDir()));
	}
	
	protected function _setCollectionOrder($column)
	{
		if ($column->getOrderCallback()) {
			call_user_func($column->getOrderCallback(), $this->getCollection(), $column);

			return $this;
		}

		return parent::_setCollectionOrder($column);
	}
}

