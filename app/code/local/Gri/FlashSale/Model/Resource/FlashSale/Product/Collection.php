<?php

/**
 * @method Gri_FlashSale_Model_Resource_FlashSale_Product getResource()
 */
class Gri_FlashSale_Model_Resource_FlashSale_Product_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{

    protected function _construct()
    {
        $this->_init('gri_flashsale/flashSale_product');
    }

    public function joinColorOrderedTable()
    {
        if (!isset($this->_joinedTables[$alias = 'poc'])) {
            $this->getSelect()->joinLeft(
                array($alias => $this->getResource()->getOrderedTable()),
                implode(' AND ', array(
                    $alias . '.flash_sale_id=main_table.flash_sale_id',
                    $alias . '.parent_id=main_table.parent_id',
                    $alias . '.color_code=main_table.color_code',
                )),
                array('color_qty_ordered')
            );
            $this->_joinedTables[$alias] = TRUE;
        }
        return $this;
    }

    public function joinOrderedTable()
    {
        if (!isset($this->_joinedTables[$alias = 'po'])) {
            $this->getSelect()->joinLeft(
                array($alias => $this->getResource()->getOrderedTable()),
                implode(' AND ', array(
                    $alias . '.flash_sale_id=main_table.flash_sale_id',
                    $alias . '.product_id=main_table.product_id',
                )),
                array('qty_ordered', 'parent_qty_ordered')
            );
            $this->_joinedTables[$alias] = TRUE;
        }
        return $this;
    }
}
