<?php
class Gri_CatalogSearch_Block_Top_Search extends Mage_Core_Block_Template
{
    protected $_brands = NULL;

    protected $_categoryOptions = NULL;

    protected $_colorOptions = NULL;

    protected $_sizeOptions = NULL;

    public function getBrandOptions()
    {
         if($this->_brands == NULL){
             $this->_brands= Mage::getResourceModel('catalog/product')
                 ->getAttribute('brand')
                 ->getSource()
                 ->getAllOptions();

             $allowBrands = Mage::helper('gri_catalogcustom')->getLeftMenuArr();
             $brandNamesHaystack = '';
             foreach($allowBrands as $key => $brand){
                 if($brand['sort'] < 3) {
                     unset($allowBrands[$key]);
                 }else{
                     $brandNamesHaystack .= $brand['label'].' ';
                 }
             }

             foreach($this->_brands as $key => $brand) {
                 if($brand['label'] && strpos(strtolower($brandNamesHaystack), strtolower($brand['label'])) == FALSE){
                     unset($this->_brands[$key]);
                 }
             }

            $this->_brands[0]['label'] = $this->__('Select Brands');
         }

         return $this->_brands;
    }

    /**
     * @return Mage_Catalog_Model_Resource_Category_Collection
     */
    public function getCategoryOptions()
    {
        if($this->_categoryOptions == NULL) {
            $this->_categoryOptions = Mage::getSingleton('catalog/category')->getCollection()
                                        ->addNameToResult()
                                        ->addIsActiveFilter()
                                        ->addFieldToFilter('url_key', array('shoes','clothing','bags','accessories'))
                                        ->addLevelFilter(2);
        }

        return $this->_categoryOptions;
    }

    public function getColorOptions()
    {
        if($this->_colorOptions == NULL){
            $this->_colorOptions = Mage::getResourceModel('catalog/product')
                ->getAttribute('color_filter_1')
                ->getSource()
                ->getAllOptions();
            $this->_colorOptions[0]['label'] = $this->__('All Colors');
        }

        return $this->_colorOptions;
    }

    public function getSizeOptions()
    {
        if($this->_sizeOptions == NULL){
            $this->_sizeOptions = Mage::getResourceModel('catalog/product')
                ->getAttribute('size_filter')
                ->getSource()
                ->getAllOptions();
            $this->_sizeOptions[0]['label'] = $this->__('All Size');
        }
        return $this->_sizeOptions;
    }

    /**
     * @param $code
     * @return  Mage_Eav_Model_Entity_Attribute_Abstract|false
     */
    protected function getAttributeByCode($code)
    {
        return Mage::getSingleton('eav/config')->getAttribute('catalog_product',$code);
    }
}