<?php 

class Gri_Ubl_Model_Mysql4_Ubl extends Mage_Core_Model_Mysql4_Abstract
{

	protected $_isPkAutoIncrement = false;

	public function _construct()
	{
		$this->_init('ubl/ubl','user_id');
	}
}
