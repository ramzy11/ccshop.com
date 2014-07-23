<?php
class Gri_Cms_Block_Page extends Mage_Cms_Block_Page
{
    public $brands = array(
        'ninewest',
        'stevemadden',
        'betseyjohnson',
        'eqiq',
        'carolinnaespinosa',
        'jeannepierre',
    );

    public function _prepareLayout()
    {
        $cmsHelper = $this->helper('cms');
        /* @var $breadcrumbs Mage_Page_Block_Html_Breadcrumbs */
        $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
        $breadcrumbs and $breadcrumbs->addCrumb('home', array());
        $page = $this->getPage();
        $page->getContentHeading() and $page->setTitle($page->getContentHeading());
        $storeName = Mage::getStoreConfig(Mage_Core_Model_Store::XML_PATH_STORE_STORE_NAME);
        $page->getMetaKeywords() or $page->setMetaKeywords($storeName . ' - ' . $page->getTitle());
        $page->getMetaDescription() or $page->setMetaDescription($storeName . ' - ' . $page->getTitle());
        $data = explode('/', $page->getIdentifier());
        if (count($data) && $brand = array_intersect($this->brands, $data)) {
            /* @var $category Gri_CatalogCustom_Model_Category */
            $category = Mage::getModel('catalog/category')->loadByAttribute('url_key', reset($brand));
            if ($category) {
                if (Mage::getStoreConfig('web/default/show_cms_breadcrumbs')
                    && $breadcrumbs
                    && ($page->getIdentifier() !== Mage::getStoreConfig('web/default/cms_home_page'))
                    && ($page->getIdentifier() !== Mage::getStoreConfig('web/default/cms_no_route'))
                ) {
                    $breadcrumbs->addCrumb('brand', array('label' => $category->getName(), 'title' => $category->getName(), 'link' => Mage::helper('catalog/category')->getCategoryUrl($category)));
                }
            }
        }
        /* @var $node Gri_Cms_Model_Hierarchy_Node */
        else if ($breadcrumbs && $node = Mage::registry('current_cms_hierarchy_node')) {
            /* @var $parent Gri_Cms_Model_Hierarchy_Node */
            foreach ($node->getParentNodes() as $parent) {
                $breadcrumbs->addCrumb('hierarchy', array(
                    'label' => $parent->getLabel(),
                    'title' => $parent->getLabel(),
                   // 'label' => $cmsHelper->__($parent->getLabel()),
                   // 'title' => $cmsHelper->__($parent->getLabel()),
                   //'link' => $parent->getUrl(),
                ));
            }
        }
        return parent::_prepareLayout();
    }
}
