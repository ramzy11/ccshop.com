<?php

class Gri_FlashSale_ProductController extends Gri_FlashSale_Controller_Abstract
{

    public function viewAction()
    {
        $flashSale = $this->_initFlashSale();
        if ($flashSale->getId()) {
            $sku = $this->getRequest()->getParam('item');
            /* @var $productModel Gri_CatalogCustom_Model_Product */
            $productModel = Mage::getModel('catalog/product');
            $productId = $productModel->getIdBySku($sku);
            $flashSaleProduct = $flashSale->getParentProductById($productId);
            if ($flashSaleProduct && $flashSaleProduct->getId()) {
                Mage::register('remove_unavailable_products', TRUE);
                Mage::register('flash_sale_product', $flashSaleProduct);
                $this->getRequest()->setParam('id', $productId);
                $this->_forward('view', 'product', 'catalog');
            }
            else $this->_forward('noRoute');
        }
        else $this->_forward('noRoute');
    }
}
