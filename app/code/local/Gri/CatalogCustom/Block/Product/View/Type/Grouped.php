<?php

class Gri_CatalogCustom_Block_Product_View_Type_Grouped extends Mage_Catalog_Block_Product_View_Type_Grouped {

    public function backupCurrentProduct() {
        return $this->setCurrentProduct($this->getProduct());
    }

    /**
     * @return Mage_Core_Model_Layout
     */
    public function getChildLayout(Mage_Catalog_Model_Product $product) {
        if ($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_GROUPED)
            return FALSE;

        /* @var $layout Mage_Core_Model_Layout */
        $layout = Mage::getModel('core/layout');
        $layout->setArea($this->getLayout()->getArea());
        foreach ($this->getLayout()->getUpdate()->getHandles() as $handel) {
            $handel == 'catalog_product_view' and $handel = 'quickshop_index_view';
            $handel == 'PRODUCT_TYPE_grouped' and $handel = 'PRODUCT_TYPE_' . $product->getTypeId();
            $handel == 'PRODUCT_' . $this->getCurrentProduct()->getId() and $handel = 'PRODUCT_' . $product->getId();
            $layout->getUpdate()->addHandle($handel);
        }
        $layout->getUpdate()->addHandle('quickshop_index_view_FINAL');
        $layout->getUpdate()->load();
        foreach ($layout->getAllBlocks() as $k => $block) {
            $layout->unsetBlock($k);
            $block->unsetData();
            unset($block);
        }
        $layout->generateXml()->generateBlocks();
        return $layout;
    }

    public function renderChildProduct(Mage_Catalog_Model_Product $product) {
        Mage::unregister('current_product');
        Mage::unregister('product');
        Mage::register('current_product', $product);
        Mage::register('product', $product);
        if ($layout = $this->getChildLayout($product)) {
            $block = $layout->getBlock('product.info');
            $block->setProduct($product);
            return $block->toHtml();
        }
        return FALSE;
    }

    public function restoreCurrentProduct() {
        Mage::unregister('current_product');
        Mage::unregister('product');
        Mage::register('current_product', $this->getCurrentProduct());
        Mage::register('product', $this->getCurrentProduct());
        $this->setProduct($this->getCurrentProduct());
        return $this;
    }

}
