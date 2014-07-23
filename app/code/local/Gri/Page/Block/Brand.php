<?php
class Gri_Page_Block_Brand extends Mage_Core_Block_Template
{

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        /* @var $root Mage_Page_Block_Html */
        if ($root = $this->getLayout()->getBlock('root')) {
            $this->getBrand() and $root->addBodyClass('brand-' . $this->getBrand()->getUrlKey());
        }
        return $this;
    }

    /**
     * get Brand from current page
     */
    public function getBrand()
    {
        if ($this->getData('brand') === NULL) {
            if (NULL !== $brand = Mage::registry('brand_category')) return $brand;
            $params = $this->getRequest()->getParams();
            // category case and product case
            if (in_array($this->getRequest()->getRouteName(), array("catalog", "gri_catalogcustom"))) {
                $params = $this->getRequest()->getParams();
                if ($this->getRequest()->getControllerName() == 'category') {
                    if (isset($params['id'])) {
                        $category = Mage::getModel('catalog/category')->load($params['id']);
                        $brand = $category->getBrandCategory();
                    }
                }
                if (!$brand && $this->getRequest()->getControllerName() == 'product') {
                    if (isset($params['category'])) {
                        $category = Mage::getModel('catalog/category')->load($params['category']);
                        $brand = $category->getBrandCategory();
                    }
                }
            }
            // cms page case
            if (!$brand && in_array($this->getRequest()->getRouteName(), array('cms', 'gri_cms'))) {
                if (isset($params['page_id'])) {
                    $page = Mage::getModel('cms/page')->load($params['page_id']);
                    if ($page) {
                        $brand = Mage::helper('gri_cms/page')->getBrand($page);
                    }
                }
            }
            $this->setData('brand', $brand);
            Mage::register('brand_category', $brand);
        }
        return $this->getData('brand');
    }

    public function getImageUrl($image){
        return $image ? Mage::getBaseUrl('media').'catalog/category/'.$image : false;
    }

    public function getImageDir($image){
        return $image ? Mage::getBaseDir('media').DS.'catalog'.DS.'category'.DS .$image : false;
    }

    public function getSortedBrandsHtml()
    {
        $html = '<ul class="brand-links">';
        /* @var $categoryBrandHelper Gri_CatalogCustom_Helper_Category */
        $categoryBrandHelper = Mage::helper('gri_catalogcustom/category');
        $brandCategories = $categoryBrandHelper->getBrandCategories(Mage::app()->getStore());
        $sortedBrandKeys = Mage::helper('gri_catalogcustom')->getStoreBrands();

        $li = array();
        foreach ($brandCategories as $brandCategorie){
            $li[$brandCategorie->getUrlKey()] = '<li><a href="'.Mage::getUrl($brandCategorie->getUrlPath()).'">
            <img src="'.$this->getImageUrl($brandCategorie->getThumbnail()).'" title="'.$brandCategorie->getName().'" class="brand-bottom-'.$brandCategorie->getUrlKey().'" /></a></li>';
        }

        $sorted_li= '';
        foreach($sortedBrandKeys as $key){
            if(isset($li[$key])){
                $sorted_li .= $li[$key];
            }
        }

        return $html . $sorted_li . '</ul>';
    }
}
