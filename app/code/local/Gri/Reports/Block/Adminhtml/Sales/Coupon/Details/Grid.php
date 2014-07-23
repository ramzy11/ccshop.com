<?php

/**
 * Class Gri_Reports_Block_Adminhtml_Sales_Coupon_Details_Grid
 * @method Gri_Reports_Model_Resource_Report_Coupon_Collection getCollection
 */
class Gri_Reports_Block_Adminhtml_Sales_Coupon_Details_Grid extends Gri_Reports_Block_Adminhtml_Sales_Grid_Abstract
{
    protected $_columnGroupBy = 'rule_id';
    protected $_resourceCollectionName = 'gri_reports/report_coupon_collection';

    protected function _prepareCollection()
    {
        parent::_prepareCollection();
        $filterData = $this->getFilterData();
        $collection = $this->getCollection();
        if (!$collection && ($this->getRequest()->getParam('filter') !== NULL ||
            strtolower(substr($this->getRequest()->getActionName(), 0, 6)) == 'export'
        )) {
            $this->setCollection($collection = Mage::getResourceModel($this->getResourceCollectionName())
                ->setDateRange($filterData->getData('from', null), $filterData->getData('to', null)));
        }
        if ($collection) {
            $this->setCountTotals(TRUE);
            $this->setCountSubTotals(TRUE);
            $collection->setColumnGroupBy($this->_columnGroupBy);
            $collection->getSelect()->joinLeft(array('o' => $collection->getTable('sales/order')), 'o.coupon_code=t.code');
            $collection->addExpressionFieldToSelect('used_at', 'o.created_at', array())
                ->addExpressionFieldToSelect('used', '(o.entity_id IS NOT NULL)', array())
                ->addExpressionFieldToSelect('sold_amount', '(o.base_grand_total - o.base_shipping_amount)', array());
            if ($ruleId = $filterData->getRuleId()) {
                $collection->addFieldToFilter('t.rule_id', $ruleId);
            }
            if ($ruleName = $filterData->getRuleName()) {
                $collection->addFieldToFilter('r.name', $ruleName);
            }
            if ($couponCode = $filterData->getCouponCode()) {
                $collection->addFieldToFilter('t.code', $couponCode);
            }
            if ($used = $filterData->getUsed()) {
                $collection->addFieldToFilter('o.entity_id', array($used == 1 ? 'notnull' : 'null' => 1));
            }
            $totals = Mage::getModel('adminhtml/report_item');
            $totalKeys = array();
            foreach ($this->getColumns() as $column) {
                if ($column->getTotal() == 'sum') $totalKeys[] = $column->getIndex();
            }
            foreach ($collection as $item) {
                foreach ($totalKeys as $key) {
                    $totals->setData($key, $totals->getData($key) + $item->getData($key));
                }
            }
            $this->setTotals($totals);
            $this->getIsExport() or $collection->groupData();
        } else {
            $this->setCollection(Mage::getModel('reports/grouped_collection'));
        }
        return $this;
    }

    protected function _prepareColumns()
    {
        $this->addColumn('rule_id', array(
            'header' => $this->__('Price Rule ID'),
            'index' => 'rule_id',
            'width' => 100,
            'sortable' => FALSE,
            'type' => 'number',
            'totals_label' => Mage::helper('sales')->__('Total'),
            'subtotals_label' => Mage::helper('sales')->__('Subtotal'),
            'html_decorators' => array('nobr'),
        ));

        $this->addColumn('name', array(
            'header' => $this->__('Price Rule Name'),
            'index' => 'name',
            'sortable' => FALSE,
        ));

        $this->addColumn('code', array(
            'header' => $this->__('Coupon Code'),
            'index' => 'code',
            'sortable' => FALSE,
        ));

        $this->addColumn('customer_email', array(
            'header' => $this->__('Customer Email'),
            'index' => 'customer_email',
            'sortable' => FALSE,
        ));

        $this->addColumn('used', array(
            'header'    => $this->__('Used'),
            'index'     => 'used',
            'type'  => 'options',
            'options' => Mage::getSingleton('adminhtml/system_config_source_yesno')->toArray(),
            'sortable'  => FALSE,
        ));

        $this->addColumn('used_at', array(
            'header' => $this->__('Used at Time'),
            'index' => 'used_at',
            'sortable' => FALSE,
            'type' => 'datetime',
        ));

        $this->addColumn('increment_id', array(
            'header' => $this->__('Order #'),
            'index' => 'increment_id',
            'sortable' => FALSE,
        ));

        $currency = (string)Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE);
        $this->addColumn('sold_amount', array(
            'header' => $this->__('Coupon Sold Amount'),
            'index' => 'sold_amount',
            'type' => 'currency',
            'currency_code' => $currency,
            'sortable' => FALSE,
        ));

        $this->addColumn('discount_amount', array(
            'header' => $this->__('Discount Amount'),
            'index' => 'base_discount_amount',
            'type' => 'currency',
            'currency_code' => $currency,
            'sortable' => FALSE,
        ));

        $this->addExportType('*/*/exportCouponDetailsExcel', Mage::helper('adminhtml')->__('Excel XML'));
        $this->addExportType('*/*/exportCouponDetailsCsv', Mage::helper('adminhtml')->__('CSV'));

        return parent::_prepareColumns();
    }
}
