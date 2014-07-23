<?php
class Gri_Preorder_Block_Product_List extends Mage_Catalog_Block_Product_List
{

    public $mode = 'general';

    /**
     * @return Gri_CatalogCustom_Model_Category
     */
    public function getCurrentCategory()
    {
        if (!$this->hasData('current_category')) {
            $this->setData('current_category', Mage::registry('current_category'));
        }
        return $this->getData('current_category');
    }

    public function getEditorsPickProductCollection()
    {
        return $this->getCurrentCategory()->getEditorsPickCollection();
    }

    public function getBestSellerCollection()
    {
        return $this->getCurrentCategory()->getBestSellerCollection();
    }

    /**
     *  
     */
    public function getLoadedProductCollection()
    {
        $productCollection = $this->_getProductCollection();  
        if (in_array($this->getRequest()->getRouteName(), array('catalogsearch', 'gri_catalogsearch')) && $this->getRequest()->getControllerName() == 'result') {
            
            
            if ($this->getRequest()->getParam('brand')) {
                $productCollection->addFieldToFilter('brand', $this->getRequest()->getParam('brand'));
            }
            
            // define search priority sort in search result
            $productCollection->addAttributeToSort('priority', 'desc');
            $productCollection->addAttributeToSort('created_at', 'desc');
        }
        return $productCollection;
    }
    
    /**
     *  getter preorder product collection
     * 
     *  @return 
     */
    public  function _getProductCollection(){ 
        $page = $this->getPage();
        //$stockItemProductIds = $this->getInstockProductIds();
        //$stockItemProductIds = implode(',', $stockItemProductIds);
        
        // $collection = Mage::getResourceModel('catalog/product_collection')->setStoreId(0);
        $collection = Mage::getModel('catalog/product')->getResourceCollection();
        
        $collection->addAttributeToSelect('*')
                  // ->addAttributeToSelect('id', array('in' => $stockItemProductIds)) 
                   ->addAttributeToFilter('type_id','configurable') 
                   //->addAttributeToFilter('preorder_from_date' , array('gteq' => $this->_getNowDate()))
                   ->addAttributeToFilter('preorder_to_date' , array('from'=> $this->_getNowDate()))
                   ->setPage($page,9)
                   ->load();
        
        return $collection;
    }
    
    
    public   function getPage(){
       $page = Mage::app()->getRequest()->getParam('p');
       $page = intval($page);       
       return  $page ? $page :1 ;
    }
    
    /**
     *  getter now time
     *  
     */
    protected function _getNowDate(){
       return Mage::getSingleton('gri_preorder/preorder')->_getNowDate();
    }
    
    protected  function  getInstockProductIds(){
       $products_id_arr = array();
       $stockCollection = Mage::getModel('cataloginventory/stock_item')->getCollection()
                          ->addFieldToFilter('is_in_stock', 1);
       
       foreach($stockCollection as $item){
         $products_id_arr[] = $item->getProductId();     
       }
       
       return $products_id_arr ;       
    }
       
}