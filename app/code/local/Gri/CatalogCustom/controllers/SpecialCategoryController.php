<?php

/**
 * Class Gri_CatalogCustom_SpecialCategoryController
 * @see /includes/config.php for redirect implemention
 */
class Gri_CatalogCustom_SpecialCategoryController extends Mage_Core_Controller_Front_Action
{

    protected function _processForward($type)
    {
        $category = $this->getRequest()->getParam('path', FALSE);
        $product = $this->getRequest()->getParam('product');
        try {
            /* @var $urlRewriteModel Mage_Core_Model_Url_Rewrite */
            $urlRewriteModel = Mage::getModel('core/url_rewrite');
            $urlRewriteModel->setStoreId($storeId = Mage::app()->getStore()->getId());
            if ($category) {
                $category = base64_decode($category) . '/' . $type .
                    Mage::getStoreConfig(Mage_Catalog_Helper_Category::XML_PATH_CATEGORY_URL_SUFFIX);
                $category = $urlRewriteModel->loadByRequestPath($category)->getCategoryId();
                $category = Mage::getModel('catalog/category')->setStoreId($storeId)->load($category);
                Mage::registry('current_category') === NULL && $category->getId() and
                    Mage::register('current_category', $category);
            }
            if ($product) {
                $product = $urlRewriteModel->loadByRequestPath($product)->getProductId();
            }
        }
        catch (Exception $e) {
            Mage::logException($e);
        }
        $this->getRequest()->setParam('id', $product);
        $this->_forward('view', 'product', 'catalog');
    }

    public function bestSellersAction()
    {
        $this->_processForward('best-sellers');
    }

    public function editorsPickAction()
    {
        $this->_processForward('editor');
    }

    public function newArrivalsAction()
    {
        $this->_processForward('new-arrivals');
    }

    public function preOrderAction()
    {
        $this->_processForward('pre-order');
    }

    public function preSalesAction()
    {
        $this->_processForward('pre-sales');
    }
}
