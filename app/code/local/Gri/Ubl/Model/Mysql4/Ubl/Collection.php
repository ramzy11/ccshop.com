<?php

class Gri_Ubl_Model_Mysql4_Ubl_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	public function _construct()
	{
		parent::_construct();
		$this->_init('ubl/ubl');
	}
}
