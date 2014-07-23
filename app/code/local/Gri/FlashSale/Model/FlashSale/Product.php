<?php

/**
 * @method Gri_FlashSale_Model_Resource_FlashSale_Product_Collection getCollection()
 * @method integer getFlashSaleId() Get Flash Sale Id
 * @method integer getParentId() Get Parent Product Id
 * @method integer getQty() Get Salable Qty
 * @method Gri_FlashSale_Model_Resource_FlashSale_Product getResource()
 * @method Gri_FlashSale_Model_FlashSale_Product setFlashSaleId(integer $flashSaleId) Set Flash Sale Id
 */
class Gri_FlashSale_Model_FlashSale_Product extends Mage_Core_Model_Abstract
{
    protected $_subProducts = array();

    protected function _construct()
    {
        $this->_init('gri_flashsale/flashSale_product');
    }

    public function getFlashSale()
    {
        if ($this->getData('flash_sale') === NULL && $this->getFlashSaleId()) {
            /* @var $flashSale Gri_FlashSale_Model_FlashSale */
            $flashSale = Mage::getModel('gri_flashsale/flashSale')->load($this->getFlashSaleId());
            $this->setFlashSale($flashSale);
        }
        return $this->getData('flash_sale');
    }

    public function getIsActive()
    {
        // TODO Apply sales quantity checking
        return TRUE;
    }

    /**
     * @return Gri_CatalogCustom_Model_Product
     */
    public function getProduct()
    {
        if ($this->getData('product') === NULL) {
            $product = Mage::getModel('catalog/product')->load($this->getId());
            $this->setData('product', $product);
        }
        return $this->getData('product');
    }

    public function getQtyOrdered()
    {
        if ($this->_getData('qty_ordered') === NULL) {
            $this->setData('qty_ordered', $this->getResource()->getQtyOrdered($this));
        }
        return $this->_getData('qty_ordered');
    }

    /**
     * @param bool $availableOnly
     * @return Gri_FlashSale_Model_Resource_FlashSale_Product_Collection
     */
    public function getSubProducts($availableOnly = TRUE)
    {
        if (!isset($this->_subProducts[$availableOnly])) {
            $collection = $this->getCollection();
            $collection->addFieldToFilter('main_table.flash_sale_id', $this->getFlashSaleId())
                ->addFieldToFilter('main_table.parent_id', $this->getParentId())
            ;
            $collection->joinOrderedTable()->joinColorOrderedTable();
            $columns = $collection->getSelect()->getPart('columns');
            array_unshift($columns, array(NULL, new Zend_Db_Expr('DISTINCT(`main_table`.`product_id`)'), 'pid'));
            $collection->getSelect()->setPart('columns', $columns);
            if ($availableOnly) {
                $parentQtyOrdered = $this->getResource()->getParentQtyOrdered($this) * 1;
                $collection->getSelect()
                    ->where('main_table.qty = 0 OR (main_table.qty > po.qty_ordered) OR (po.qty_ordered IS NULL)')
                    ->where('main_table.color_qty = 0 OR (main_table.color_qty > poc.color_qty_ordered) OR (poc.color_qty_ordered IS NULL)')
                    ->where('main_table.parent_qty = 0 OR (main_table.parent_qty > ?)', $parentQtyOrdered);
            }
            $this->_subProducts[$availableOnly] = $collection;
        }
        return $this->_subProducts[$availableOnly];
    }

    public function load($id, $field = NULL)
    {
        $field === NULL and $field = 'product_id';
        return parent::load($id, $field);
    }

    public function setFlashSale(Gri_FlashSale_Model_FlashSale $flashSale)
    {
        return $this->setFlashSaleId($flashSale->getId())->setData('flash_sale', $flashSale);
    }
}
