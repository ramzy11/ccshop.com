<?php

class Gri_Vip_Model_Mysql4_Offline_Pk_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	public function _construct()
	{
		parent::_construct();
		$this->_init('gri_vip/offline_pk');
	}

}
