<?php


class Gri_Vip_Model_Mysql4_Offline_Pk extends Mage_Core_Model_Mysql4_Abstract
{

	public function _construct()
	{
		$this->_init('gri_vip/offline_pk','id');
	}

}
