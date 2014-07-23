<?php

class Gri_FlashSale_IndexController extends Gri_FlashSale_Controller_Abstract
{

    /**
     * @return Gri_CatalogCustom_Model_Category
     */
    protected function _prepareFakeCategory()
    {
        $flashSale = $this->_initFlashSale();
        $category = Mage::getModel('catalog/category');
        Mage::register('current_category', $category);
        $category->setName($flashSale->getTitle())->setIsActive(1)->setIsAnchor(1)
            ->setUrlKey('flashsale')->setDisplayMode($category::DM_MIXED);

        $category->setProductCollection($flashSale->getAssociatedProducts());
        return $category;
    }

    public function indexAction()
    {
        $this->_prepareFakeCategory();
        /* @var $flashSaleHelper Gri_FlashSale_Helper_Data */
        $flashSaleHelper = Mage::helper('gri_flashsale');
        $flashSaleHelper->setRemoveUnavailableOptions();
        Mage::register('disable_sold_out_links', TRUE);
        $this->loadLayout();
        if ($this->getRequest()->getParam('isAjax')) {
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array (
                'filterMenus' => $this->getLayout()->getBlock('mana.catalog.filternav')->toHtml(),
                'categoryProducts' => $this->getLayout()->getBlock('product_list')->toHtml()
            )));
        }
        else {
            /* @var $breadcrumbs Mage_Page_Block_Html_Breadcrumbs */
            $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
            $breadcrumbs->addCrumb('flashsale', array(
                'label' => $this->_getHelper()->__('Flash Sale'),
                'title' => $this->_getHelper()->__('Flash Sale'),
            ));

            if ($root = $this->getLayout()->getBlock('root')) {
                $root->addBodyClass('flash-sale-list');
            }
            $this->_title($this->_getHelper()->__('Flash Sale'));
            $this->_initLayoutMessages('catalog/session');
            $this->_initLayoutMessages('checkout/session');
            $this->renderLayout();
        }
    }
}
