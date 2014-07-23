<?php

require_once ('app/code/core/Mage/Catalog/controllers/ProductController.php');
class Gri_CatalogCustom_ProductController extends Mage_Catalog_ProductController
{
    public function quickviewAction()
    {
        // Get initial data from request
        $categoryId = (int) $this->getRequest()->getParam('category', false);
        $productId  = (int) $this->getRequest()->getParam('id');
        $specifyOptions = $this->getRequest()->getParam('options');

        // Prepare helper and params
        /* @var $viewHelper Mage_Catalog_Helper_Product_View */
        $viewHelper = Mage::helper('catalog/product_view');

        $params = new Varien_Object();
        $params->setCategoryId($categoryId);
        $params->setSpecifyOptions($specifyOptions);

        /* @var $activeFlashSale Gri_FlashSale_Model_FlashSale */
        $flashSale = Mage::helper('gri_flashsale')->getActiveFlashSale();
        if ($flashSale->getId()) {
            $flashSaleProduct = $flashSale->getParentProductById($productId);
            if ($flashSaleProduct && $flashSaleProduct->getId()) {
                Mage::register('remove_unavailable_products', FALSE);
                Mage::register('flash_sale_product', $flashSaleProduct);
            }
        }

        // Render page
        try {
            $viewHelper->prepareAndRender($productId, $this, $params);
        } catch (Exception $e) {
            if ($e->getCode() == $viewHelper->ERR_NO_PRODUCT_LOADED) {
                if (isset($_GET['store'])  && !$this->getResponse()->isRedirect()) {
                    $this->_redirect('');
                } elseif (!$this->getResponse()->isRedirect()) {
                    $this->_forward('noRoute');
                }
            } else {
                Mage::logException($e);
                $this->_forward('noRoute');
            }
        }
    }
}
