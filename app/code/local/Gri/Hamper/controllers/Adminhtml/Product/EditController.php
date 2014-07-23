<?php

class_exists('Mage_Adminhtml_Catalog_ProductController', FALSE) or include 'Mage/Adminhtml/controllers/Catalog/ProductController.php';

class Gri_Hamper_Adminhtml_Product_EditController extends Mage_Adminhtml_Catalog_ProductController
{

    protected function _construct()
    {
        $this->setUsedModuleName('Gri_Hamper');
    }

    public function formAction()
    {
        $product = $this->_initProduct();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('hamper/adminhtml_catalog_product_edit_tab_hamper', 'admin.product.hamper.items')
                ->setProductId($product->getId())
                ->toHtml()
        );
    }
}
