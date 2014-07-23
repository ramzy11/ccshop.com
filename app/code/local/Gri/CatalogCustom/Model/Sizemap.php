<?php

class Gri_CatalogCustom_Model_Sizemap extends Mage_Core_Model_Abstract
{

	/**
	 * Initialize resource model
	 */
	protected function _construct()
	{
		$this->_init('gri_catalogcustom/sizemap');
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
}