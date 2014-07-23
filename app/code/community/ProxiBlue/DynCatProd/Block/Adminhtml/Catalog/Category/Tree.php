<?php

/**
 * Functions to handle the left category tree, taking dynamic categories into consideration
 *
 * @category   ProxiBlue
 * @package    DynCatProd
 * @author     Lucas van Staden (sales@proxiblue.com.au)
 */

class ProxiBlue_DynCatProd_Block_Adminhtml_Catalog_Category_Tree extends Mage_Adminhtml_Block_Catalog_Category_Tree {

    /**
     * Get JSON of array of categories, that are breadcrumbs for specified category path
     *
     * @param string $path
     * @param string $javascriptVarName
     * @return string
     */
    public function getBreadcrumbsJavascript($path, $javascriptVarName) {
        if (empty($path)) {
            return '';
        }

        $categories = Mage::getResourceSingleton('catalog/category_tree')
                        ->setStoreId($this->getStore()->getId())->loadBreadcrumbsArray($path);
        if (empty($categories)) {
            return '';
        }
        foreach ($categories as $key => $category) {
            $collection = Mage::getModel('catalog/product')->getCollection()
                    ->addAttributeToSelect('sku');
            //->addStoreFilter($this->getRequest()->getParam('store'))
            $count = 0;

            if (Mage::helper('dyncatprod')->addDynamicFilters($collection, $category['entity_id'])) {
                //$collection->load();
                $count = $collection->count();
                $category['product_count'] += $count;
            }
            $categories[$key] = $this->_getNodeJson($category);
        }
        return
                '<script type="text/javascript">'
                . $javascriptVarName . ' = ' . Mage::helper('core')->jsonEncode($categories) . ';'
                . ($this->canAddSubCategory() ? '$("add_subcategory_button").show();' : '$("add_subcategory_button").hide();')
                . '</script>';
    }

}
