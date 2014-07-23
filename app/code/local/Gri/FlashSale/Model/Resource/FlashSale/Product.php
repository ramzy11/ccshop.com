<?php

class Gri_FlashSale_Model_Resource_FlashSale_Product extends Mage_Core_Model_Resource_Db_Abstract
{

    protected function _construct()
    {
        $this->_init('gri_flashsale/flashsale_product', 'product_id');
    }

    /**
     * @param string $field
     * @param mixed $value
     * @param Gri_FlashSale_Model_FlashSale_Product $object
     * @return Varien_Db_Select|Zend_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $field  = $this->_getReadAdapter()->quoteIdentifier(sprintf('%s.%s', $this->getMainTable(), $field));
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable())
            ->where($field . '=?', $value)
            ->where('flash_sale_id=?', $object->getFlashSaleId());
        return $select;
    }

    /**
     * @param Gri_FlashSale_Model_FlashSale_Product $object
     * @return float
     */
    public function getColorQtyOrdered($object)
    {
        if ($object->getData('color_qty_ordered') === NULL) {
            $qty = $this->_getReadAdapter()->fetchOne('SELECT color_qty_ordered FROM ' .
                $this->getOrderedTable() . ' WHERE flash_sale_id = ? AND parent_id = ? AND color_code = ? ORDER BY color_qty_ordered DESC',
                array($object->getFlashSaleId(), $object->getParentId(), $object->getColorCode()));
            is_numeric($qty) or $qty = FALSE;
            $object->setData('color_qty_ordered', $qty);
        }
        return $object->getData('color_qty_ordered');
    }

    public function getOrderedTable()
    {
        return $this->getTable('gri_flashsale/flashsale_product_ordered');
    }

    /**
     * @param Gri_FlashSale_Model_FlashSale_Product $object
     * @return float
     */
    public function getParentQtyOrdered($object)
    {
        if ($object->getData('parent_qty_ordered') === NULL) {
            $qty = $this->_getReadAdapter()->fetchOne('SELECT parent_qty_ordered FROM ' .
                $this->getOrderedTable() . ' WHERE flash_sale_id = ? AND parent_id = ? ORDER BY parent_qty_ordered DESC',
                array($object->getFlashSaleId(), $object->getParentId()));
            is_numeric($qty) or $qty = FALSE;
            $object->setData('parent_qty_ordered', $qty);
        }
        return $object->getData('parent_qty_ordered');
    }

    /**
     * @param Gri_FlashSale_Model_FlashSale_Product $object
     * @return float
     */
    public function getQtyOrdered($object)
    {
        if ($object->getData('qty_ordered') === NULL) {
            $qty = $this->_getReadAdapter()->fetchOne('SELECT qty_ordered FROM ' .
                    $this->getOrderedTable() . ' WHERE flash_sale_id = ? AND product_id = ?',
                array($object->getFlashSaleId(), $object->getProductId()));
            is_numeric($qty) or $qty = FALSE;
            $object->setData('qty_ordered', $qty);
        }
        return $object->getData('qty_ordered');
    }

    public function insertMultiple(array $data, $table = NULL)
    {
        $table === NULL and $table = $this->getMainTable();
        strpos($table, '/') and $table = $this->getTable($table);
        $tableDefinition = $this->getReadConnection()->describeTable($table);
        foreach ($data as $k => $row) {
            foreach ($row as $col => $v) {
                if (!isset($tableDefinition[$col])) unset($row[$col]);
            }
            $data[$k] = $row;
        }
        $this->_getWriteAdapter()->insertOnDuplicate($table, $data);
        return $this;
    }

    public function removeAll()
    {
        $this->_getWriteAdapter()->query('DELETE FROM `' . $this->getMainTable() . '`');
        return $this;
    }

    /**
     * @param Gri_FlashSale_Model_FlashSale_Product $object
     * @param float $qtyDelta
     * @return Gri_FlashSale_Model_Resource_FlashSale_Product
     */
    public function updateColorQtyOrdered($object, $qtyDelta)
    {
        $object->setColorQtyOrdered($qtyDelta + $oldQty = $this->getColorQtyOrdered($object));
        $this->_getWriteAdapter()->update($this->getOrderedTable(), array(
            'color_qty_ordered' => $object->getColorQtyOrdered(),
        ), array(
            'flash_sale_id = ?' => $object->getFlashSaleId(),
            'parent_id = ?' => $object->getParentId(),
            'color_code = ?' => $object->getColorCode(),
        ));
        return $this;
    }

    /**
     * @param Gri_FlashSale_Model_FlashSale_Product $object
     * @param float $qtyDelta
     * @return Gri_FlashSale_Model_Resource_FlashSale_Product
     */
    public function updateParentQtyOrdered($object, $qtyDelta)
    {
        $object->setParentQtyOrdered($qtyDelta + $oldQty = $this->getParentQtyOrdered($object));
        $this->_getWriteAdapter()->update($this->getOrderedTable(), array(
            'parent_qty_ordered' => $object->getParentQtyOrdered(),
        ), array(
            'flash_sale_id = ?' => $object->getFlashSaleId(),
            'parent_id = ?' => $object->getParentId(),
        ));
        return $this;
    }

    /**
     * @param Gri_FlashSale_Model_FlashSale_Product $object
     * @param float $qtyDelta
     * @return Gri_FlashSale_Model_Resource_FlashSale_Product
     */
    public function updateQtyOrdered($object, $qtyDelta)
    {
        $object->setQtyOrdered($qtyDelta + $oldQty = $this->getQtyOrdered($object));
        if ($oldQty === FALSE) $this->_getWriteAdapter()->insert($this->getOrderedTable(), array(
            'flash_sale_id' => $object->getFlashSaleId(),
            'product_id' => $object->getProductId(),
            'parent_id' => $object->getParentId(),
            'color_code' => $object->getColorCode(),
            'qty_ordered' => $object->getQtyOrdered(),
        ));
        else $this->_getWriteAdapter()->update($this->getOrderedTable(), array(
            'qty_ordered' => $object->getQtyOrdered(),
        ), array(
            'flash_sale_id = ?' => $object->getFlashSaleId(),
            'product_id = ?' => $object->getProductId(),
        ));
        return $this;
    }
}
