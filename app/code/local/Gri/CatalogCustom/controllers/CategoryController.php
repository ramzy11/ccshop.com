<?php

require_once ('app/code/core/Mage/Catalog/controllers/CategoryController.php');

class Gri_CatalogCustom_CategoryController extends Mage_Catalog_CategoryController
{

    public function groupAction()
    {
        /* @var $group Gri_CatalogCustom_Block_Category_Group */
        $group = $this->getLayout()->createBlock('gri_catalogcustom/category_group', 'category_group', array(
            'template' => 'catalog/category/group_ajax.phtml',
            'category_ids' => (int)$this->getRequest()->getParam('id'),
            'limit' => (int)$this->getRequest()->getParam('size'),
            'page' => (int)$this->getRequest()->getParam('page'),
        ));
        $data = array('html' => $group->toHtml());
        $data = Zend_Json::encode($data);
        ($callback = $this->getRequest()->getParam('callback')) and $data = $callback . '(' . $data . ');';
        $this->getResponse()->setHeader('Content-type', $callback ? 'text/javascript' : 'text/json')->setBody($data);
    }

    public function viewAction()
    {
        if ($category = $this->_initCatagory()) {
            $design = Mage::getSingleton('catalog/design');
            $settings = $design->getDesignSettings($category);

            // apply custom design
            if ($settings->getCustomDesign()) {
                $design->applyCustomDesign($settings->getCustomDesign());
            }

            Mage::getSingleton('catalog/session')->setLastViewedCategoryId($category->getId());

            $update = $this->getLayout()->getUpdate();
            $update->addHandle('default');

            if (!$category->hasChildren()) {
                $update->addHandle('catalog_category_layered_nochildren');
            }

            $this->addActionLayoutHandles();
            $update->addHandle($category->getLayoutUpdateHandle());
            $update->addHandle('CATEGORY_' . $category->getId());
            $this->loadLayoutUpdates();

            // apply custom layout update once layout is loaded
            if ($layoutUpdates = $settings->getLayoutUpdates()) {
                if (is_array($layoutUpdates)) {
                    foreach ($layoutUpdates as $layoutUpdate) {
                        $update->addUpdate($layoutUpdate);
                    }
                }
            }

            $this->generateLayoutXml()->generateLayoutBlocks();
            // apply custom layout (page) template once the blocks are generated
            if ($settings->getPageLayout()) {
                $this->getLayout()->helper('page/layout')->applyTemplate($settings->getPageLayout());
            }

            if ($root = $this->getLayout()->getBlock('root')){
                $root->addBodyClass('category-root-path-' . Mage::helper('gri_catalogcustom/category')->getRootCategoryUrlKey($category))
                     ->addBodyClass('categorypath-' . $category->getUrlPath())
                     ->addBodyClass('category-' . $category->getUrlKey());
            }
            // ajax
            $params = $this->getRequest()->getParams();
            if (isset($params['isAjax']) && $params['isAjax'] == 1) {
                $response = array();
                $this->loadLayout();
                $response['categoryProducts'] = $this->getLayout()->getBlock('product_list')->toHtml();
                $response['filter'] = $this->getLayout()->getBlock('mana.catalog.filternav')->toHtml();
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
                return;
            }
            $this->_initLayoutMessages('catalog/session');
            $this->_initLayoutMessages('checkout/session');
            $this->renderLayout();
        } elseif (!$this->getResponse()->isRedirect()) {
            $this->_forward('noRoute');
        }
    }

}
