<?php
class Gri_CountryGroup_Adminhtml_CountryGroupController extends Mage_Adminhtml_Controller_Action {

	public function indexAction() {
		$this->_title($this->__('Gri'))
		->_title($this->__('Manage Country Group'));
		$this->loadLayout();
		$this->renderLayout();
	}

	public function editAction() {
		$this->_title($this->__('Gri'))
		->_title($this->__('Manage Country Group'));
		$sizemapId  = (int) $this->getRequest()->getParam('id');
		$sizemap   = Mage::getModel('gri_countrygroup/countrygroup')->load($sizemapId);
		if ($sizemapId && !$sizemap->getData('country_group_id')) {
			$this->_getSession()->addError(Mage::helper('gri_countrygroup')->__('This country group no longer exists.'));
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
		$this->loadLayout();
		$this->renderLayout();
	}

	public function newAction()
	{
		$this->editAction();
	}

	public function saveAction(){
		$storeId        = $this->getRequest()->getParam('store');
		$name  			= $this->getRequest()->getParam('name');
		$redirectBack   = $this->getRequest()->getParam('back', false);
		$countrygroupId = $this->getRequest()->getParam('id');
		$countries      = $this->getRequest()->getParam('countries');
		if($countries) $countries=implode(',', $countries);
		$isEdit         = (int)($this->getRequest()->getParam('id') != null);
		$countrygroup  = Mage::getModel('gri_countrygroup/countrygroup')->load($countrygroupId);
		$data = $this->getRequest()->getPost();
		if ($data) {
			try {
				$countrygroup->setData("name",$name);
				$countrygroup->setData('value',$countries);
				$countrygroup->save();
				$countrygroupId = $countrygroup->getId();
				$this->_getSession()->addSuccess($this->__('The Country Group info has been saved.'));
			} catch (Mage_Core_Exception $e) {
				$this->_getSession()->addError($e->getMessage())
				->setCountryGroupData($data);
				$redirectBack = true;
			} catch (Exception $e) {
				Mage::logException($e);
				$this->_getSession()->addError($e->getMessage());
				$redirectBack = true;
			}
		}

		if ($redirectBack) {
			$this->_redirect('*/*/edit', array(
				'id'    => $countrygroupId,
				'_current'=>true
			));
		} elseif($this->getRequest()->getParam('popup')) {
			$this->_redirect('*/*/created', array(
				'_current'   => true,
				'id'         => $countrygroupId,
				'edit'       => $isEdit
			));
		} else {
			$this->_redirect('*/*/', array('store'=>$storeId));
		}

	}


	/**
	 * Delete country group action
	 */
	public function deleteAction()
	{
		if ($id = $this->getRequest()->getParam('id')) {
			$countrygroup = Mage::getModel('gri_countrygroup/countrygroup')
			->load($id);
			try {
				$countrygroup->delete();
				$this->_getSession()->addSuccess($this->__('The countrygroup has been deleted.'));
			} catch (Exception $e) {
				$this->_getSession()->addError($e->getMessage());
			}
		}
		$this->getResponse()
		->setRedirect($this->getUrl('*/*/', array('store'=>$this->getRequest()->getParam('store'))));
	}

	public function validateAction()
	{
		$response = new Varien_Object();
		$response->setError(false);
		$name  = $this->getRequest()->getParam('name');
		$id    = (int)$this->getRequest()->getParam('id',false);
		$countrygroup = Mage::getModel('gri_countrygroup/countrygroup')
		->loadByAttribute('name',$name);
		if (!$id && $countrygroup ) {
			Mage::getSingleton('adminhtml/session')->addError(
				Mage::helper('gri_countrygroup')->__('Country Group with the same name already exists'));
			$this->_initLayoutMessages('adminhtml/session');
			$response->setError(true);
			$response->setMessage($this->getLayout()->getMessagesBlock()->getGroupedHtml());
		}

		$this->getResponse()->setBody($response->toJson());
	}

	public function massDeleteAction()
	{
		$countrygroupIds = $this->getRequest()->getParam('countrygroup');
		if (!is_array($countrygroupIds)) {
			$this->_getSession()->addError($this->__('Please select item(s).'));
		} else {
			if (!empty($countrygroupIds)) {
				try {
					foreach ($countrygroupIds as $countrygroupId) {
						$countrygroup = Mage::getSingleton('gri_countrygroup/countrygroup')->load($countrygroupId);
						//Mage::dispatchEvent('catalog_controller_countrygroup_delete', array('countrygroup' => $countrygroup));
						$countrygroup->delete();
					}
					$this->_getSession()->addSuccess(
						$this->__('Total of %d record(s) have been deleted.', count($countrygroupIds))
					);
				} catch (Exception $e) {
					$this->_getSession()->addError($e->getMessage());
				}
			}
		}
		$this->_redirect('*/*/index');
	}


}