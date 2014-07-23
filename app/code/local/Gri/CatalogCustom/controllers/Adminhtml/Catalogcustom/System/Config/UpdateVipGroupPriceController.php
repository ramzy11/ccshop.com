<?php
class Gri_CatalogCustom_Adminhtml_Catalogcustom_System_Config_UpdateVipGroupPriceController extends Mage_Adminhtml_Controller_Action {
          
     public function  indexAction(){
         if($this->getRequest()->getParam('isAjax') ){
             $config = Mage::getSingleton('core/config') ;
             
             //save  vip discount percents ;
             $offlinevipDiscountPercents = $this->getRequest()->getParam('offlinevip');
             $silverDiscountPercents =  $this->getRequest()->getParam('silver');
             $goldDiscpuntPercents = $this->getRequest()->getParam('gold');
            
             $config->saveConfig('gri_vip/discount/offlinevip' ,$offlinevipDiscountPercents);
             $config->saveConfig('gri_vip/discount/silver' ,$silverDiscountPercents);
             $config->saveConfig('gri_vip/discount/gold' ,$goldDiscpuntPercents);
             
             // init  store config cache
             Mage::getConfig()->reinit();
             
             $data =  Mage:: getSingleton('gri_catalogcustom/product')->updateAllProductsVipGroupPrice() ? 1 : 0 ;      
             
         }else{
           $data =   0;
         }
         
         echo $data ;
     }     
}