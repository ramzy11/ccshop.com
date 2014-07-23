<?php

class Gri_Reports_Adminhtml_CustomersController extends Gri_Reports_Controller_Adminhtml_Abstract
{

    public function consumptionSummaryAction()
    {
        $this->_title($this->__('GRI Reports'))->_title($this->__('Customer'))->_title($this->__('Consumption'));

        $this->_initAction()
            ->_setActiveMenu('report/gri_customers/consumptionsummary')
            ->_addBreadcrumb($this->__('Consumption'), $this->__('Summary'));

        $gridBlock = $this->getLayout()->getBlock('adminhtml_customers_consumption.grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction(array(
            $gridBlock,
            $filterFormBlock
        ));

        $this->renderLayout();
    }

    public function exportConsumptionSummaryCsvAction()
    {
        $this->_exportCsv('gri_reports/adminhtml_customers_consumption_grid', 'customers_consumption.csv');
    }

    public function exportConsumptionSummaryExcelAction()
    {
        $this->_exportExcel('gri_reports/adminhtml_customers_consumption_grid', 'customers_consumption.xml');
    }
}
