<?php
class Gri_CountryGroup_Model_Resource_Countrygroup_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
	public function _construct()
	{
		parent::_construct();
		$this->_init('gri_countrygroup/countrygroup');
	}

}
?>
