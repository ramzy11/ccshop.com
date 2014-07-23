<?php

class Gri_Reports_Adminhtml_SalesController extends Gri_Reports_Controller_Adminhtml_Abstract
{

    public function _initAction()
    {
        parent::_initAction();
        $this->_addBreadcrumb(Mage::helper('reports')->__('Sales'), Mage::helper('reports')->__('Sales'));
        return $this;
    }

    public function discountSummaryAction()
    {
        $this->_title($this->__('GRI Reports'))->_title($this->__('Sales'))->_title($this->__('Discount Summary'));

        $this->_initAction()
            ->_setActiveMenu('report/gri_sales/discountSummary')
            ->_addBreadcrumb($this->__('Discount Summary'), $this->__('Discount Summary'));

        $gridBlock = $this->getLayout()->getBlock('adminhtml_sales_discount_summary.grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction(array(
            $gridBlock,
            $filterFormBlock
        ));

        $this->renderLayout();
    }

    public function exportDiscountSummaryCsvAction()
    {
        $this->_exportCsv('gri_reports/adminhtml_sales_discount_summary_grid', 'discount_summary.csv');
    }

    public function exportDiscountSummaryExcelAction()
    {
        $this->_exportExcel('gri_reports/adminhtml_sales_discount_summary_grid', 'discount_summary.xml');
    }

    public function categorySummaryAction()
    {
        $this->_title($this->__('GRI Reports'))->_title($this->__('Sales'))->_title($this->__('Category Summary'));

        $this->_initAction()
            ->_setActiveMenu('report/gri_sales/categorySummary')
            ->_addBreadcrumb($this->__('Category Summary'), $this->__('Category Summary'));

        $gridBlock = $this->getLayout()->getBlock('adminhtml_sales_category_summary.grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction(array(
            $gridBlock,
            $filterFormBlock
        ));

        $this->renderLayout();
    }

    public function exportCategorySummaryCsvAction()
    {
        $this->_exportCsv('gri_reports/adminhtml_sales_category_summary_grid', 'category_summary.csv');
    }

    public function exportCategorySummaryExcelAction()
    {
        $this->_exportExcel('gri_reports/adminhtml_sales_category_summary_grid', 'category_summary.xml');
    }

    public function couponDetailsAction()
    {
        $this->_title($this->__('GRI Reports'))->_title($this->__('Sales'))->_title($this->__('Coupon Details'));

        $this->_initAction()
            ->_setActiveMenu('report/gri_sales/couponDetails')
            ->_addBreadcrumb($this->__('Coupon Details'), $this->__('Coupon Details'));

        $gridBlock = $this->getLayout()->getBlock('adminhtml_sales_coupon_details.grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction(array(
            $gridBlock,
            $filterFormBlock,
        ));

        $this->renderLayout();
    }

    public function exportCouponDetailsCsvAction()
    {
        $this->_exportCsv('gri_reports/adminhtml_sales_coupon_details_grid', 'coupon_details.csv');
    }

    public function exportCouponDetailsExcelAction()
    {
        $this->_exportExcel('gri_reports/adminhtml_sales_coupon_details_grid', 'coupon_details.xml');
    }

    public function couponSummaryAction()
    {
        $this->_title($this->__('GRI Reports'))->_title($this->__('Sales'))->_title($this->__('Coupon Summary'));

        $this->_initAction()
            ->_setActiveMenu('report/gri_sales/couponSummary')
            ->_addBreadcrumb($this->__('Coupon Summary'), $this->__('Coupon Summary'));

        $gridBlock = $this->getLayout()->getBlock('adminhtml_sales_coupon_summary.grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction(array(
            $gridBlock,
            $filterFormBlock,
        ));

        $this->renderLayout();
    }

    public function exportCouponSummaryCsvAction()
    {
        $this->_exportCsv('gri_reports/adminhtml_sales_coupon_summary_grid', 'coupon_summary.csv');
    }

    public function exportCouponSummaryExcelAction()
    {
        $this->_exportExcel('gri_reports/adminhtml_sales_coupon_summary_grid', 'coupon_summary.xml');
    }

    public function financialAction()
    {
        $this->_title($this->__('GRI Reports'))->_title($this->__('Sales'))->_title($this->__('Financial'));

        $this->_initAction()
            ->_setActiveMenu('report/gri_sales/financial')
            ->_addBreadcrumb($this->__('Financial'), $this->__('Financial'));

        $gridBlock = $this->getLayout()->getBlock('adminhtml_sales_financial.grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction(array(
            $gridBlock,
            $filterFormBlock,
        ));

        $this->renderLayout();
    }

    public function exportFinancialCsvAction()
    {
        $this->_exportCsv('gri_reports/adminhtml_sales_financial_grid', 'financial.csv');
    }

    public function exportFinancialExcelAction()
    {
        $this->_exportExcel('gri_reports/adminhtml_sales_financial_grid', 'financial.xml');
    }

    public function orderDetailsAction()
    {
        $this->_title($this->__('GRI Reports'))->_title($this->__('Sales'))->_title($this->__('Order'))->_title($this->__('Details'));

        $this->_initAction()
            ->_setActiveMenu('report/gri_sales/orderdetails')
            ->_addBreadcrumb($this->__('Orders'), $this->__('Details'));

        $gridBlock = $this->getLayout()->getBlock('adminhtml_sales_order_detail.grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction(array(
            $gridBlock,
            $filterFormBlock
        ));

        $this->renderLayout();
    }

    public function exportOrderDetailsCsvAction()
    {
        $this->_exportCsv('gri_reports/adminhtml_sales_order_detail_grid', 'order_detail.csv');
    }

    public function exportOrderDetailsExcelAction()
    {
        $this->_exportExcel('gri_reports/adminhtml_sales_order_detail_grid', 'order_detail.xml');
    }

    public function ordersAction()
    {
        $this->_title($this->__('GRI Reports'))->_title($this->__('Sales'))->_title($this->__('Orders'));

        $this->_initAction()
            ->_setActiveMenu('report/gri_sales/orders')
            ->_addBreadcrumb($this->__('Orders'), $this->__('Orders'));

        $gridBlock = $this->getLayout()->getBlock('adminhtml_sales_order.grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction(array(
            $gridBlock,
            $filterFormBlock
        ));

        $this->renderLayout();
    }

    public function exportOrdersCsvAction()
    {
        $this->_exportCsv('gri_reports/adminhtml_sales_order_grid', 'order.csv');
    }

    public function exportOrdersExcelAction()
    {
        $this->_exportExcel('gri_reports/adminhtml_sales_order_grid', 'order.xml');
    }

    public function productSummaryAction()
    {
        $this->_title($this->__('GRI Reports'))->_title($this->__('Sales'))->_title($this->__('Product Summary'));

        $this->_initAction()
            ->_setActiveMenu('report/gri_sales/productsummary')
            ->_addBreadcrumb($this->__('Product Summary'), $this->__('Product Summary'));

        $gridBlock = $this->getLayout()->getBlock('adminhtml_sales_product_summary.grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction(array(
            $gridBlock,
            $filterFormBlock
        ));

        $this->renderLayout();
    }

    public function exportProductSummaryCsvAction()
    {
        $this->_exportCsv('gri_reports/adminhtml_sales_product_summary_grid', 'product_summary.csv');
    }

    public function exportProductSummaryExcelAction()
    {
        $this->_exportExcel('gri_reports/adminhtml_sales_product_summary_grid', 'product_summary.xml');
    }

    public function rmaDetailsAction()
    {
        $this->_title($this->__('GRI Reports'))->_title($this->__('Sales'))->_title($this->__('RMA Details'));

        $this->_initAction()
            ->_setActiveMenu('report/gri_sales/rmaDetails')
            ->_addBreadcrumb($this->__('RMA Details'), $this->__('RMA Details'));

        $gridBlock = $this->getLayout()->getBlock('adminhtml_sales_rma_details.grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction(array(
            $gridBlock,
            $filterFormBlock
        ));

        $this->renderLayout();
    }

    public function exportRmaDetailsCsvAction()
    {
        $this->_exportCsv('gri_reports/adminhtml_sales_rma_details_grid', 'rma_details.csv');
    }

    public function exportRmaDetailsExcelAction()
    {
        $this->_exportExcel('gri_reports/adminhtml_sales_rma_details_grid', 'rma_details.xml');
    }

    public function summaryAction()
    {
        $this->_title($this->__('GRI Reports'))->_title($this->__('Sales'))->_title($this->__('Summary'));

        $this->_initAction()
            ->_setActiveMenu('report/gri_sales/summary')
            ->_addBreadcrumb($this->__('Summary'), $this->__('Summary'));

        $gridBlock = $this->getLayout()->getBlock('adminhtml_sales_summary.grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction(array(
            $gridBlock,
            $filterFormBlock
        ));

        $this->renderLayout();
    }

    public function exportSummaryCsvAction()
    {
        $this->_exportCsv('gri_reports/adminhtml_sales_summary_grid', 'summary.csv');
    }

    public function exportSummaryExcelAction()
    {
        $this->_exportExcel('gri_reports/adminhtml_sales_summary_grid', 'summary.xml');
    }
}
