<?php

class Gri_CountryGroup_Model_Resource_Countrygroup extends Mage_Core_Model_Resource_Db_Abstract
{

	/**
	 * Initialize resource model
	 *
	 */
	protected function _construct()
	{
		$this->_init('gri_countrygroup/countrygroup', 'country_group_id');
	}
}
