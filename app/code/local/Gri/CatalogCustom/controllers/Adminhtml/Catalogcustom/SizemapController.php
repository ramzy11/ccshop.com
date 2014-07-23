<?php
class Gri_CatalogCustom_Adminhtml_Catalogcustom_SizemapController extends Mage_Adminhtml_Controller_Action {

	public function indexAction() {
		$this->_title($this->__('Gri'))
			->_title($this->__('Manage Size Map'));
		$this->loadLayout();
		$this->renderLayout();
	}
	protected function _construct()
	{
		// Define module dependent translate
		$this->setUsedModuleName('Gri_CatalogCustom');
	}

  public function editAction()
    {
    	$this->_title($this->__('Gri'))
    	->_title($this->__('Manage Size Map'));
        $sizemapId  = (int) $this->getRequest()->getParam('id');
        $sizemap   = Mage::getModel('gri_catalogcustom/sizemap')->load($sizemapId);
        if ($sizemapId && !$sizemap->getData('mapping_id')) {
            $this->_getSession()->addError(Mage::helper('gri_catalog')->__('This size map no longer exists.'));
            $this->_redirect('*/*/');
            return;
        }
        if (!Mage::app()->isSingleStoreMode() && ($switchBlock = $this->getLayout()->getBlock('store_switcher'))) {
            $switchBlock->setDefaultStoreName($this->__('Default Values'))
                ->setWebsiteIds($product->getWebsiteIds())
                ->setSwitchUrl(
                    $this->getUrl('*/*/*', array('_current'=>true, 'active_tab'=>null, 'tab' => null, 'store'=>null))
                );
        }
        //$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->loadLayout();
        $this->renderLayout();
    }

    protected function _initProductSave()
    {
    	$product     = $this->_initProduct();
    	$productData = $this->getRequest()->getPost('product');
    	if ($productData) {
    		$this->_filterStockData($productData['stock_data']);
    	}

    	/**
    	 * Websites
    	 */
    	if (!isset($productData['website_ids'])) {
    		$productData['website_ids'] = array();
    	}

    	$wasLockedMedia = false;
    	if ($product->isLockedAttribute('media')) {
    		$product->unlockAttribute('media');
    		$wasLockedMedia = true;
    	}

    	$product->addData($productData);

    	if ($wasLockedMedia) {
    		$product->lockAttribute('media');
    	}

    	if (Mage::app()->isSingleStoreMode()) {
    		$product->setWebsiteIds(array(Mage::app()->getStore(true)->getWebsite()->getId()));
    	}

    	/**
    	 * Create Permanent Redirect for old URL key
    	 */
    	if ($product->getId() && isset($productData['url_key_create_redirect']))
    		// && $product->getOrigData('url_key') != $product->getData('url_key')
    	{
    		$product->setData('save_rewrites_history', (bool)$productData['url_key_create_redirect']);
    	}

    	/**
    	 * Check "Use Default Value" checkboxes values
    	 */
    	if ($useDefaults = $this->getRequest()->getPost('use_default')) {
    		foreach ($useDefaults as $attributeCode) {
    			$product->setData($attributeCode, false);
    		}
    	}

    	/**
    	 * Init product links data (related, upsell, crosssel)
    	 */
    	$links = $this->getRequest()->getPost('links');
    	if (isset($links['related']) && !$product->getRelatedReadonly()) {
    		$product->setRelatedLinkData(Mage::helper('adminhtml/js')->decodeGridSerializedInput($links['related']));
    	}
    	if (isset($links['upsell']) && !$product->getUpsellReadonly()) {
    		$product->setUpSellLinkData(Mage::helper('adminhtml/js')->decodeGridSerializedInput($links['upsell']));
    	}
    	if (isset($links['crosssell']) && !$product->getCrosssellReadonly()) {
    		$product->setCrossSellLinkData(Mage::helper('adminhtml/js')
    			->decodeGridSerializedInput($links['crosssell']));
    	}
    	if (isset($links['grouped']) && !$product->getGroupedReadonly()) {
    		$product->setGroupedLinkData(Mage::helper('adminhtml/js')->decodeGridSerializedInput($links['grouped']));
    	}

    	/**
    	 * Initialize product categories
    	 */
    	$categoryIds = $this->getRequest()->getPost('category_ids');
    	if (null !== $categoryIds) {
    		if (empty($categoryIds)) {
    			$categoryIds = array();
    		}
    		$product->setCategoryIds($categoryIds);
    	}

    	/**
    	 * Initialize data for configurable product
    	 */
    	if (($data = $this->getRequest()->getPost('configurable_products_data'))
    		&& !$product->getConfigurableReadonly()
    	) {
    		$product->setConfigurableProductsData(Mage::helper('core')->jsonDecode($data));
    	}
    	if (($data = $this->getRequest()->getPost('configurable_attributes_data'))
    		&& !$product->getConfigurableReadonly()
    	) {
    		$product->setConfigurableAttributesData(Mage::helper('core')->jsonDecode($data));
    	}

    	$product->setCanSaveConfigurableAttributes(
    		(bool) $this->getRequest()->getPost('affect_configurable_product_attributes')
    		&& !$product->getConfigurableReadonly()
    	);

    	/**
    	 * Initialize product options
    	 */
    	if (isset($productData['options']) && !$product->getOptionsReadonly()) {
    		$product->setProductOptions($productData['options']);
    	}

    	$product->setCanSaveCustomOptions(
    		(bool)$this->getRequest()->getPost('affect_product_custom_options')
    		&& !$product->getOptionsReadonly()
    	);

    	Mage::dispatchEvent(
    		'catalog_product_prepare_save',
    		array('product' => $product, 'request' => $this->getRequest())
    	);

    	return $product;
    }

    public function saveAction() {
    	$storeId        = $this->getRequest()->getParam('store');
    	$universal_size  = $this->getRequest()->getParam('universal_size');
    	$redirectBack   = $this->getRequest()->getParam('back', false);
    	$sizemapId      = $this->getRequest()->getParam('id');
    	$isEdit         = (int)($this->getRequest()->getParam('id') != null);
    	$sizemap  = Mage::getModel('gri_catalogcustom/sizemap')->load($sizemapId);
    	$data = $this->getRequest()->getPost();
    	if ($data) {
    		try {
    			$sizemap->setData("universal_size",$universal_size);
    			$sizemap->save();
    			$sizemapId = $sizemap->getId();
    			$this->_getSession()->addSuccess($this->__('The Size Map info has been saved.'));
    		} catch (Mage_Core_Exception $e) {
    			$this->_getSession()->addError($e->getMessage())
    			->setProductData($data);
    			$redirectBack = true;
    		} catch (Exception $e) {
    			Mage::logException($e);
    			$this->_getSession()->addError($e->getMessage());
    			$redirectBack = true;
    		}
    	}

    	if ($redirectBack) {
    		$this->_redirect('*/*/edit', array(
    			'id'    => $sizemapId,
    			'_current'=>true
    		));
    	} elseif($this->getRequest()->getParam('popup')) {
    		$this->_redirect('*/*/created', array(
    			'_current'   => true,
    			'id'         => $sizemapId,
    			'edit'       => $isEdit
    		));
    	} else {
    		$this->_redirect('*/*/', array('store'=>$storeId));
    	}

    }
    public function gridAction() {
    	$this->loadLayout();
    	$this->renderLayout();
    }
}

?>