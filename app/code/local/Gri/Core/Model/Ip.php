<?php

class Gri_Core_Model_Ip extends Mage_Core_Model_Abstract
{

    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('gri_core/ip');
    }

    /**
     * @return Varien_Db_Statement_Pdo_Mysql
     */
    protected function _getStatement()
    {
        if ($this->getData('_statement') === NULL) {
            /* @var $statement Varien_Db_Statement_Pdo_Mysql */
            $statement = $this->getResource()->getReadConnection()->prepare("SELECT `country_code`
            FROM `{$this->getResource()->getMainTable()}`
            WHERE `from` <= ?
            ORDER BY `from` DESC
            LIMIT 1");
            $this->setData('_statement', $statement);
        }
        return $this->getData('_statement');
    }

	public function isFromChinaOffice($ip)
	{
		//China Office IP: 116.246.15.194 - 198 
		!is_numeric($ip) or $ip = sprintf('%s', long2ip($ip));
		return preg_match("/116\.246\.15\.19[4-8]{1}/", $ip);
	}
	
    public function ipToCountry($ip)
    {
        is_numeric($ip) or $ip = sprintf('%u', ip2long($ip));
        if (!Mage::registry($key = 'ip_to_country_' . $ip)) {
            $this->_getStatement()->execute(array($ip));
            Mage::register($key, $this->_getStatement()->fetchColumn());
        }
        return Mage::registry($key);
    }
}
