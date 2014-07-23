<?php

class Magestore_Promotionalgift_Block_Ajaxcart extends Mage_Core_Block_Template {

    /**
     * get Helper for Ajaxcart module
     * 
     * @return Magestore_Ajaxcart_Helper_Data
     */
    public function getAjaxcartHelper() {
        return Mage::helper('promotionalgift');
    }
	
	public function getPromotionalgiftHelper() {
        return Mage::helper('promotionalgift');
    }

    public function _prepareLayout() {
        if(Mage::helper('core')->isModuleEnabled('Magestore_Ajaxcart')){
			if(Mage::getStoreConfig('ajaxcart/general/enable',Mage::app()->getStore(true)->getId()))
				return;
		}	
		if( $this->getPromotionalgiftHelper()->enablePromotionalgift()
			&& $this->getIsCartPage()){         
            $this->getLayout()->getBlock('head')->addJs('magestore/promotionalcartpage.js');
            $this->setTemplate('promotionalgift/ajaxcartpage.phtml');
            $this->getLayout()->getBlock('head')->addCss('css/magestore/promotionalcart/style.css');
            
            $this->addProductJs();
        }
		if( $this->getPromotionalgiftHelper()->enablePromotionalgift()
			&& $this->getIsConfigurePage()){
			 $this->setTemplate('promotionalgift/configurepage.phtml');
		}
        return parent::_prepareLayout();
    }

    /**
     * Add JS for preload
     * 
     * @return Magestore_Ajaxcart_Block_Ajaxcart
     */
    public function addProductJs() {
        if (!$this->getIsPreloadAjax()) {
            return $this;
        }
        $productJsFiles = array(
            'js' => array(
                'varien/product.js',
                // 'varien/configurable.js',
                'calendar/calendar.js',
                'calendar/calendar-setup.js'
            ),
            'skin_js' => array(
                'js/bundle.js'
            )
        );
        $headBlock = $this->getLayout()->getBlock('head');
        if (version_compare(Mage::getVersion(), '1.5.0.0', '>=')) {
            $headBlock->addJs('varien/configurable.js');
        }
        foreach ($productJsFiles['js'] as $jsFile) {
            $headBlock->addJs($jsFile);
        }
        foreach ($productJsFiles['skin_js'] as $skinJsFile) {
            $headBlock->addItem('skin_js', $skinJsFile);
        }
        return $this;
    }

    /**
     * Check config is Preload Ajax file
     * 
     * @return type
     */   

    public function getIsCartPage() {
        if (!$this->hasData('is_cart_page')) {
            $isCartPage = ($this->getFullActionName() == 'checkout_cart_index');
            $this->setData('is_cart_page', $isCartPage);
        }
        return $this->getData('is_cart_page');
    }
	
	public function getIsConfigurePage()
	{
		if (!$this->hasData('is_configure_page')) {
            $isConfigurePage = ($this->getFullActionName() == 'checkout_cart_configure');
            $this->setData('is_configure_page', $isConfigurePage);
        }
        return $this->getData('is_configure_page');
	}
   

    public function getFullActionName($delimiter = '_') {
        return $this->getRequest()->getRequestedRouteName() . $delimiter .
            $this->getRequest()->getRequestedControllerName() . $delimiter .
            $this->getRequest()->getRequestedActionName();
    }

    public function getUrlImage() {       
		return $this->getSkinUrl('images/promotionalgift/loading.gif');         
    } 
	
	public function getFreeItemIds()
	{
		$itemIds = array();
		$quoteId = Mage::getModel('checkout/session')->getQuote()->getId();
		$catalogItems = Mage::getModel('promotionalgift/quote')->getCollection()
											->addFieldToFilter('quote_id', $quoteId);
        foreach($catalogItems as $catalogItem){
			$itemIds[] = $catalogItem->getItemId();
		}
		$shoppingItems = Mage::getModel('promotionalgift/shoppingquote')->getCollection()
											->addFieldToFilter('quote_id', $quoteId);
		foreach($shoppingItems as $shoppingItem){
			$itemIds[] = $shoppingItem->getItemId();
		}		
		return implode(',', $itemIds);
	}

    public function getIsPreloadAjax() {
        return true;
    }     
}
