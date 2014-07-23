<?php

class ProxiBlue_DynCatProd_Adminhtml_Catalog_CategoryController extends Mage_Adminhtml_Controller_Action
{
    

    /**
     * Grid Action
     * Display list of products related to current category
     *
     * @return void
     */
    public function gridAction()
    {
        $categoryId = $this->getRequest()->getParam('category_id');
        $category = Mage::getModel('catalog/category')->load($categoryId);
        Mage::register('category',$category);
        Mage::register('current_category',$category);
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('dyncatprod/adminhtml_catalog_category_tab_dyncatprod_grid', 'category.dyncatprod.grid')
                ->toHtml()
        );
    }

    /**
     * Check if admin has permissions to visit related pages
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/categories');
    }
}
