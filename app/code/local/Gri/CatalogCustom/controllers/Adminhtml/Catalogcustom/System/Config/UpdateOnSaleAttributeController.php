<?php
class Gri_CatalogCustom_Adminhtml_Catalogcustom_System_Config_UpdateOnSaleAttributeController extends Mage_Adminhtml_Controller_Action
{
     protected $_sku ;

     public function indexAction(){
         $ok = 1;
         try {
             if($this->getRequest()->getParam('isAjax') ){
                 $config = Mage::getSingleton('core/config') ;
                 $sku = $this->getRequest()->getParam('sku');
                 $config->saveConfig('gri_attribute/on_sale/sku' ,$sku);

                 $sku = $this->_getGriCoreStringHelper()->convertStringToArr($sku);
                 !count($sku) && $sku = $this->_getCatalogCustomProductHelper()->getAllCPSku();
                 foreach($sku as $_sku) {
                     $this->updateOnSale($_sku);
                 }
             }
         }
         catch (Exception $e){
             $ok = 0;
             Mage::logException($e);
         }

         echo  $ok;
     }

     protected function updateOnSale($sku)
     {
         $prices = $this->_getCatalogCustomProductHelper()->getPricesBySku($sku);

         //on sale attribute
         $onSaleAttribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'on_sale');
         $onSaleAttributeId = $onSaleAttribute->getAttributeId();
         $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
         foreach($prices as $productId => $price) {
             // Update On Sale
             $connection->insertOnDuplicate($onSaleAttribute->getBackendTable(), array(
                 'entity_type_id' => 4,// catalog_product
                 'attribute_id' => $onSaleAttributeId,
                 'store_id' => 0,
                 'entity_id' => $productId,
                 'value' => $onSale = ( floatval($price['special_price']) && $price['price'] > $price['special_price'] ? 1 : 0),
             ));
        }
    }

    protected function _getCatalogCustomProductHelper()
    {
        return Mage::helper('gri_catalogcustom/product');
    }

    protected function _getGriCoreStringHelper()
    {
        return Mage::helper('gri_core/string');
    }
}