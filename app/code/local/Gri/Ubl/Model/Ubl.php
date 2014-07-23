<?php
 
class Gri_Ubl_Model_Ubl extends Mage_Core_Model_Abstract
{
	const DEFAULT_LOCALE = 'en_US';


    public function _construct() {
        parent::_construct();
        $this->_init('ubl/ubl');
    }

	public function getDefaultLocale()
	{
		return self::DEFAULT_LOCALE;
	}
}
