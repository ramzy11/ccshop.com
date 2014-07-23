<?php
class Gri_CatalogCustom_Adminhtml_Catalogcustom_EditorpickController extends Mage_Adminhtml_Controller_Action {

	public function indexAction() {
		$this->_title($this->__('Gri'))
			->_title($this->__('Manage Editor\'s Pick'));
		$this->loadLayout();
		$this->renderLayout();
	}
	protected function _construct()
	{
		// Define module dependent translate
		$this->setUsedModuleName('Gri_CatalogCustom');
	}
	/**
	 * Initialize product from request parameters
	 *
	 * @return Mage_Catalog_Model_Product
	 */
	protected function _initProduct()
	{
		$this->_title($this->__('Gri'))
		->_title($this->__('Manage Editor\'s Pick'));

		$productId  = (int) $this->getRequest()->getParam('id');
		$product    = Mage::getModel('catalog/product')
		->setStoreId($this->getRequest()->getParam('store', 0));

		if (!$productId) {
			if ($setId = (int) $this->getRequest()->getParam('set')) {
				$product->setAttributeSetId($setId);
			}

			if ($typeId = $this->getRequest()->getParam('type')) {
				$product->setTypeId($typeId);
			}
		}

		$product->setData('_edit_mode', true);
		if ($productId) {
			try {
				$product->load($productId);
			} catch (Exception $e) {
				$product->setTypeId(Mage_Catalog_Model_Product_Type::DEFAULT_TYPE);
				Mage::logException($e);
			}
		}

		$attributes = $this->getRequest()->getParam('attributes');
		if ($attributes && $product->isConfigurable() &&
			(!$productId || !$product->getTypeInstance()->getUsedProductAttributeIds())) {
			$product->getTypeInstance()->setUsedProductAttributeIds(
				explode(",", base64_decode(urldecode($attributes)))
			);
		}

		// Required attributes of simple product for configurable creation
		if ($this->getRequest()->getParam('popup')
			&& $requiredAttributes = $this->getRequest()->getParam('required')) {
			$requiredAttributes = explode(",", $requiredAttributes);
			foreach ($product->getAttributes() as $attribute) {
				if (in_array($attribute->getId(), $requiredAttributes)) {
					$attribute->setIsRequired(1);
				}
			}
		}

		if ($this->getRequest()->getParam('popup')
			&& $this->getRequest()->getParam('product')
			&& !is_array($this->getRequest()->getParam('product'))
			&& $this->getRequest()->getParam('id', false) === false) {

			$configProduct = Mage::getModel('catalog/product')
			->setStoreId(0)
			->load($this->getRequest()->getParam('product'))
			->setTypeId($this->getRequest()->getParam('type'));

			/* @var $configProduct Mage_Catalog_Model_Product */
			$data = array();
			foreach ($configProduct->getTypeInstance()->getEditableAttributes() as $attribute) {

				/* @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
				if(!$attribute->getIsUnique()
					&& $attribute->getFrontend()->getInputType()!='gallery'
					&& $attribute->getAttributeCode() != 'required_options'
					&& $attribute->getAttributeCode() != 'has_options'
					&& $attribute->getAttributeCode() != $configProduct->getIdFieldName()) {
					$data[$attribute->getAttributeCode()] = $configProduct->getData($attribute->getAttributeCode());
				}
			}

			$product->addData($data)
			->setWebsiteIds($configProduct->getWebsiteIds());
		}

		Mage::register('product', $product);
		Mage::register('current_product', $product);
		Mage::getSingleton('cms/wysiwyg_config')->setStoreId($this->getRequest()->getParam('store'));
		return $product;
	}
  public function editAction()
    {
        $productId  = (int) $this->getRequest()->getParam('id');
        $product = $this->_initProduct();

        if ($productId && !$product->getId()) {
            $this->_getSession()->addError(Mage::helper('catalog')->__('This product no longer exists.'));
            $this->_redirect('*/*/');
            return;
        }

        $this->_title($product->getName());

        Mage::dispatchEvent('catalog_product_edit_action', array('product' => $product));

        $_additionalLayoutPart = '';
        if ($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE
            && !($product->getTypeInstance()->getUsedProductAttributeIds()))
        {
            $_additionalLayoutPart = '_new';
        }

        $this->loadLayout(array(
            'default',
            strtolower($this->getFullActionName()),
            'adminhtml_catalog_product_'.$product->getTypeId() . $_additionalLayoutPart
        ));

        $this->_setActiveMenu('catalogcustom/editor_pick');

        if (!Mage::app()->isSingleStoreMode() && ($switchBlock = $this->getLayout()->getBlock('store_switcher'))) {
            $switchBlock->setDefaultStoreName($this->__('Default Values'))
                ->setWebsiteIds($product->getWebsiteIds())
                ->setSwitchUrl(
                    $this->getUrl('*/*/*', array('_current'=>true, 'active_tab'=>null, 'tab' => null, 'store'=>null))
                );
        }

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $block = $this->getLayout()->getBlock('catalog.wysiwyg.js');
        if ($block) {
            $block->setStoreId($product->getStoreId());
        }

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
    	$editors_pick  = $this->getRequest()->getParam('editors_pick');
    	$redirectBack   = $this->getRequest()->getParam('back', false);
    	$productId      = $this->getRequest()->getParam('id');
    	$isEdit         = (int)($this->getRequest()->getParam('id') != null);

    	$data = $this->getRequest()->getPost();
    	if ($data) {
    		$product = $this->_initProduct();
    		try {
    			$product->setData("editors_pick",$editors_pick);
    			$product->save();
    			$productId = $product->getId();
    			$this->_getSession()->addSuccess($this->__('The editor\'s pick has been saved.'));
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
    			'id'    => $productId,
    			'_current'=>true
    		));
    	} elseif($this->getRequest()->getParam('popup')) {
    		$this->_redirect('*/*/created', array(
    			'_current'   => true,
    			'id'         => $productId,
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