<?php

class Gri_CountryGroup_Model_Countrygroup extends Mage_Core_Model_Abstract
{

	/**
	 * Is model deleteable
	 *
	 * @var boolean
	 */
	protected $_isDeleteable = true;

	/**
	 * Initialize resource model
	 */
	protected function _construct()
	{
		$this->_init('gri_countrygroup/countrygroup');
	}
	/**
	 * Checks model is deletable
	 *
	 * @return boolean
	 */
	public function isDeleteable()
	{
		return $this->_isDeleteable;
	}


	/**
	 * Set is deletable flag
	 *
	 * @param boolean $value
	 * @return Gri_CountryGroup_Model_CountryGroup
	 */
	public function setIsDeleteable($value)
	{
		$this->_isDeleteable = (bool) $value;
		return $this;
	}

	public function loadByAttribute($attribute, $value, $additionalAttributes = '*')
	{
		$collection = $this->getResourceCollection()
		->addFieldToFilter($attribute, array('eq'=>$value));
		foreach ($collection as $object) {
			return $object;
		}
		return false;
	}

		// update data on country_group attribute
	protected function _afterSave()
	{
		$attrId = Mage::getResourceModel('eav/entity_attribute')->getIdByCode('catalog_product', 'country_group');
		$optionCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
		->setAttributeFilter($attrId)
		->setStoreFilter(0, false)
		->load();
		$adapter = Mage::getSingleton('core/resource')->getConnection('core_write');
		$optionValueTable   = 'eav_attribute_option_value';
		$originalValue = $this->getOrigData('name');
		$oldOptionVals = array();
		foreach ($optionCollection as $_opt) {
			// update mode
			if($_opt->getValue() == $originalValue)
			{
				$data  = array('value'    => $this->getName());
				$where = array('option_id =?' => $_opt->getData('option_id'),'store_id=?'=>0,);
				$adapter->update($optionValueTable, $data, $where);
				$update = 1;
				continue;
			}
				$oldOptionVals[] = $_opt->getValue();
		}
		// create mode
		if(!isset($update)) {
			$attrSetup = new Mage_Eav_Model_Entity_Setup('core_setup');
			if (!in_array($this->getName(), $oldOptionVals)) {
				$newOpt = array();
				$newOpt['attribute_id'] = $attrId;
				$newOpt['value'][0][0] = $this->getName();
				$attrSetup->addAttributeOption($newOpt);
			}
		}
		parent::_afterSave();
	}

	protected function _afterDelete()
	{
		$label = $this->getName();
		$attrId = Mage::getResourceModel('eav/entity_attribute')->getIdByCode('catalog_product', 'country_group');
		$optionCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
		->setAttributeFilter($attrId)
		->setStoreFilter(0, false)
		->load();
		$oldOptionVals = array();
		foreach ($optionCollection as $_opt) {
			if($_opt->getValue()==$label) $_opt->delete();
		}
		parent::_afterDelete();
	}
}