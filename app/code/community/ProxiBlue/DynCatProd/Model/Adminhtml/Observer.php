<?php

/**
 * Observer events
 *
 * @category   ProxiBlue
 * @package    DynCatProd
 * @author     Lucas van Staden (sales@proxiblue.com.au)
 */

class ProxiBlue_DynCatProd_Model_Adminhtml_Observer {

    
    /**
     * Event to add Gifts Tab to Category edit display
     * 
     * @param Varien_Event_Observer $observer
     * @return ProxiBlue_DynCatProd_Model_Adminhtml_Observer 
     */
    public function adminhtml_catalog_category_tabs(Varien_Event_Observer $observer) {
        try {
            $tabs = $observer->getEvent()->getTabs();
            $tabBlock = $tabs->getLayout()->createBlock(
                        'dyncatprod/adminhtml_catalog_category_tab_dyncatprod', 'category.dyncatprod.tab'
            );
            $gridBlock = $tabs->getLayout()->createBlock(
                        'dyncatprod/adminhtml_catalog_category_tab_dyncatprod_grid', 'category.dyncatprod.grid'
            );
            $tabBlock->setChild('category_dyncatprod_grid',$gridBlock);
            $tabs->addTab('dyncatprod', array(
                'label' => Mage::helper('catalog')->__('Dynamic Products'),
                'content' => $tabBlock->toHtml(),
            ));
        } catch (Exception $e) {
            // log any issues, but allow system to continue.    
            Mage::logException($e);
            //mage::throwException($e->getMessage());
        }
        return $this;
    }
    
    
    /**
     * Event to adjust the product edit grid and ad din the Gifts Tab
     * 
     * @param Varien_Event_Observer $observer
     * @return ProxiBlue_GiftProducts_Model_Adminhtml_Observer 
     */
    public function adminhtml_block_html_before(Varien_Event_Observer $observer) {
        try {
            $block = $observer->getEvent()->getBlock();
            if ($block instanceof Mage_Adminhtml_Block_Catalog_Category_Tab_Product && $block->getId() == "catalog_category_products") {                
                $block->setTemplate('dyncatprod/widget/grid.phtml');
            }
        } catch (Exception $e) {
            // log any issues, but allow system to continue.    
            Mage::logException($e);
            Mage::throwException($e->getMessage());
        }
        return $this;
    }
    
    /**
     * Event to save admin catalog product save gift products
     * 
     * @param Varien_Event_Observer $observer
     * @return ProxiBlue_DynCatProd_Model_Adminhtml_Observer 
     */
    public function catalog_category_prepare_save(Varien_Event_Observer $observer) {
        try {
            $event = $observer->getEvent();
            if ($data = $event->getRequest()->getPost()) {
                if (isset($data['dyncatprod_attributes'])) {
                    foreach($data['dyncatprod_attributes'] as $key => $value){
                        if(!is_array($value)){
                            unset($data['dyncatprod_attributes'][$key]);
                            continue;
                        }
                        if(array_key_exists('value', $value) && strlen(trim($value['value'])) == 0) {
                            unset($data['dyncatprod_attributes'][$key]);
                            continue;
                        }
                    }
                    if(count($data['dyncatprod_attributes']) > 0){
                        $event->getCategory()->setData('dynamic_attributes', serialize($data['dyncatprod_attributes']));
                        //$event->getCategory()->setData('is_anchor',false);
                    } else {
                        $event->getCategory()->setData('dynamic_attributes','');
                    }   
                }
                unset($data['dyncatprod_attributes']);
            }
        } catch (Exception $e) {
            // log any issues, but allow system to continue.    
            Mage::logException($e);
            Mage::throwException($e->getMessage());
        }
        return $this;
    }

}
