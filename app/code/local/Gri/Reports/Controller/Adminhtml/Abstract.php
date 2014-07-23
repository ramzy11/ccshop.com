<?php

abstract class Gri_Reports_Controller_Adminhtml_Abstract extends Mage_Adminhtml_Controller_Report_Abstract
{

    protected function _exportCsv($blockName, $fileName)
    {
        $this->_getCoreHelper()->setCanEscapeHtml(FALSE);
        /* @var $grid Gri_Reports_Block_Adminhtml_Sales_Grid_Abstract */
        $grid = $this->getLayout()->createBlock($blockName);
        $this->_initReportAction($grid->setIsExport());
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    protected function _exportExcel($blockName, $fileName)
    {
        $this->_getCoreHelper()->setCanEscapeHtml(FALSE);
        /* @var $grid Gri_Reports_Block_Adminhtml_Sales_Grid_Abstract */
        $grid = $this->getLayout()->createBlock($blockName);
        $this->_initReportAction($grid->setIsExport());
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }

    /**
     * @return Gri_Core_Helper_Core_Data
     */
    protected function _getCoreHelper()
    {
        return Mage::helper('core');
    }

    /**
     * Initialize category object in registry
     *
     * @return Mage_Catalog_Model_Category
     */
    protected function _initCategory()
    {
        $categoryId = (int)$this->getRequest()->getParam('id', FALSE);
        $storeId = (int)$this->getRequest()->getParam('store');

        $category = Mage::getModel('catalog/category');
        $category->setStoreId($storeId);

        if ($categoryId) {
            $category->load($categoryId);
            if ($storeId) {
                $rootId = Mage::app()->getStore($storeId)->getRootCategoryId();
                if (!in_array($rootId, $category->getPathIds())) {
                    $this->_redirect('*/*/', array('_current' => TRUE, 'id' => NULL));
                    return FALSE;
                }
            }
        }

        Mage::register('category', $category);
        Mage::register('current_category', $category);

        return $category;
    }

    protected function _isAllowed()
    {
        $action = $this->getRequest()->getActionName();
        if (substr($action, 0, 6) == 'export') return TRUE;
        switch (TRUE) {
            case in_array($action, array(
                'productSummary',
                'discountSummary',
            )):
                $resource = 'report/products/';
                break;
            case in_array($action, array(
                'categorySummary',
            )):
                $resource = 'report/categories/';
                break;
            default:
                $resource = 'report/salesroot/';
                break;
        }
        return $this->_getSession()->isAllowed($resource . strtolower(preg_replace('/(.)([A-Z])/', "$1_$2", $action)));
    }

    public function categoriesJsonAction()
    {
        if ($categoryId = (int)$this->getRequest()->getParam('id')) {
            $this->getRequest()->setParam('id', $categoryId);

            if (!$category = $this->_initCategory()) {
                return;
            }
            Mage::register('hide_special_categories', TRUE);
            /* @var $block Mage_Adminhtml_Block_Catalog_Category_Tree */
            $block = $this->getLayout()->createBlock('adminhtml/catalog_category_tree');
            $this->getResponse()->setBody($block->getTreeJson($category));
        }
    }

    public function categoryChooserAction()
    {
        $request = $this->getRequest();

        $ids = $request->getParam('selected', array());
        if (is_array($ids)) {
            foreach ($ids as $key => $id) {
                $ids[$key] = (int)$id;
                if ($id <= 0) {
                    unset($ids[$key]);
                }
            }
            $ids = array_unique($ids);
        } else {
            $ids = array();
        }

        Mage::register('hide_special_categories', TRUE);
        /* @var $block Mage_Adminhtml_Block_Catalog_Category_Checkboxes_Tree */
        $block = $this->getLayout()->createBlock(
            'adminhtml/catalog_category_checkboxes_tree', 'gri_report_widget_chooser_category_ids',
            array('js_form_object' => $request->getParam('form', 'filter_form'))
        )->setCategoryIds($ids);

        $this->getResponse()->setBody($block->toHtml());
    }
}
