<?php
/**
 * Weibo connect template block
 * 
 * @category    Inchoo
 * @package     Inchoo_Weibo
 * @author      Ivan Weiler <ivan.weiler@gmail.com>
 * @copyright   Inchoo (http://inchoo.net)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Inchoo_Weibo_Block_Template extends Mage_Core_Block_Template
{
	
	public function isSecure()
	{
		return Mage::app()->getStore()->isCurrentlySecure();
	}
	
	public function getConnectUrl()
	{
		return $this->getUrl('inchoo_weibo/customer_account/connect', array('_secure'=>true));
	}
	
	public function getChannelUrl()
	{
		return $this->getUrl('inchoo_weibo/channel', array('_secure'=>$this->isSecure(),'locale'=>$this->getLocale()));
	}	
	
	public function getRequiredPermissions()
	{
		return json_encode('email,user_birthday');
	}
	
	public function isEnabled()
	{
		return Mage::getSingleton('inchoo_weibo/config')->isEnabled();
	}
	
	public function getApiKey()
	{
		return Mage::getSingleton('inchoo_weibo/config')->getApiKey();
	}
	
	public function getLocale()
	{
		return Mage::getSingleton('inchoo_weibo/config')->getLocale();
	}
	
        protected function _toHtml()
        {
          if (!$this->isEnabled()) {
            return '';
          }
          return parent::_toHtml();
        }
}