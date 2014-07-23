<?php
class Gri_CatalogCustom_Adminhtml_Catalogcustom_Product_Action_EditorpickController extends Mage_Adminhtml_Controller_Action
{
	public function editAction()
	{
		if (!$this->_validateProducts())  return;
		$this->loadLayout();
		$this->renderLayout();
	}

	protected function _getHelper()
	{
		return Mage::helper('adminhtml/catalog_product_edit_action_attribute');
	}

	/**
	 * Validate selection of products for massupdate
	 *
	 * @return boolean
	 */
	protected function _validateProducts()
	{
		$error = false;
		$productIds = $this->_getHelper()->getProductIds();
		if (!is_array($productIds)) {
			$error = $this->__('Please select products for attributes update');
		} else if (!Mage::getModel('catalog/product')->isProductsHasSku($productIds)) {
			$error = $this->__('Some of the processed products have no SKU value defined. Please fill it prior to performing operations on these products.');
		}

		if ($error) {
			$this->_getSession()->addError($error);
			$this->_redirect('*/catalog_product/', array('_current'=>true));
		}

		return !$error;
	}

	/**
	 * Attributes validation action
	 *
	 */
	public function validateAction()
	{
		$response = new Varien_Object();
		$response->setError(false);
		$attributesData = $this->getRequest()->getParam('attributes', array());
		$data = new Varien_Object();

		try {
			if ($attributesData) {
				$dateFormat = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
				$storeId    = $this->_getHelper()->getSelectedStoreId();

				foreach ($attributesData as $attributeCode => $value) {
					$attribute = Mage::getSingleton('eav/config')
					->getAttribute('catalog_product', $attributeCode);
					if (!$attribute->getAttributeId()) {
						unset($attributesData[$attributeCode]);
						continue;
					}
					$data->setData($attributeCode, $value);
					$attribute->getBackend()->validate($data);
				}
			}
		} catch (Mage_Eav_Model_Entity_Attribute_Exception $e) {
			$response->setError(true);
			$response->setAttribute($e->getAttributeCode());
			$response->setMessage($e->getMessage());
		} catch (Mage_Core_Exception $e) {
			$response->setError(true);
			$response->setMessage($e->getMessage());
		} catch (Exception $e) {
			$this->_getSession()->addException($e, $this->__('An error occurred while updating the product(s) attributes.'));
			$this->_initLayoutMessages('adminhtml/session');
			$response->setError(true);
			$response->setMessage($this->getLayout()->getMessagesBlock()->getGroupedHtml());
		}

		$this->getResponse()->setBody($response->toJson());
	}

	public function saveAction()
	{
		$attributesData     = $this->getRequest()->getParam('attributes', array());
		$attributeCode = 'editors_pick';
		$value = $attributesData[$attributeCode];
		$productIds = $this->_getHelper()->getProductIds();
		try {
			if ($attributesData) {
				$dateFormat = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
				$storeId    = $this->_getHelper()->getSelectedStoreId();

				foreach ($attributesData as $attributeCode => $value) {
					$attribute = Mage::getSingleton('eav/config')
					->getAttribute(Mage_Catalog_Model_Product::ENTITY, $attributeCode);
					if (!$attribute->getAttributeId()) {
						unset($attributesData[$attributeCode]);
						continue;
					}
				}

				Mage::getSingleton('catalog/product_action')
				->updateAttributes($this->_getHelper()->getProductIds(), $attributesData, $storeId);
			}
			$this->_getSession()->addSuccess(
				$this->__('Total of %d record(s) were updated', count($this->_getHelper()->getProductIds()))
			);
		}
		catch (Mage_Core_Exception $e) {
			$this->_getSession()->addError($e->getMessage());
		}
		catch (Exception $e) {
			$this->_getSession()->addException($e, $this->__('An error occurred while updating the product(s) attributes.'));
		}

		$this->_redirect('*/catalogcustom_editorpick/', array('store'=>$this->_getHelper()->getSelectedStoreId()));
	}
}
