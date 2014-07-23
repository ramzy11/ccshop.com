<?php
class Gri_CatalogCustom_Model_Resource_Sizemap_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
	public function _construct()
	{
		parent::_construct();
		$this->_init('gri_catalogcustom/sizemap');
	}

}
?>