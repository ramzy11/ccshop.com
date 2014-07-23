<?php
require_once ('app/code/core/Mage/CatalogSearch/controllers/ResultController.php');
class Gri_CatalogSearch_ResultController extends Mage_CatalogSearch_ResultController
{
	public function indexAction()
	{
		//Mage::register('current_layer', Mage::getSingleton('catalogsearch/layer'), true);
		$query = Mage::helper('catalogsearch')->getQuery();
		/* @var $query Mage_CatalogSearch_Model_Query */

		$query->setStoreId(Mage::app()->getStore()->getId());

		if ($query->getQueryText() != '') {
			if (Mage::helper('catalogsearch')->isMinQueryLength()) {
				$query->setId(0)
				->setIsActive(1)
				->setIsProcessed(1);
			}
			else {
				if ($query->getId()) {
					$query->setPopularity($query->getPopularity()+1);
				}
				else {
					$query->setPopularity(1);
				}

				if ($query->getRedirect()){
					$query->save();
					$this->getResponse()->setRedirect($query->getRedirect());
					return;
				}
				else {
					$query->prepare();
				}
			}

			Mage::helper('catalogsearch')->checkNotes();
			$this->loadLayout();
			$params = $this->getRequest()->getParams();
			if(isset($params['isAjax']) && $params['isAjax'] == 1){
				$response = array();

				$response['categoryProducts'] = $this->getLayout()->getBlock('search_result_list')->toHtml();
				//$response['filter'] = $this->getLayout()->getBlock('mana.catalog.filternav')->toHtml();
				$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
				return;
			}
			$this->_initLayoutMessages('catalog/session');
			$this->_initLayoutMessages('checkout/session');
			$this->renderLayout();

			if (!Mage::helper('catalogsearch')->isMinQueryLength()) {
				$query->save();
			}
		}
		else {
			$this->_redirectReferer();
		}
	}
}