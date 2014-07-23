<?php


class Gri_Ubl_Model_Observer 
{

	public function load_locale($observer)
	{
		$event = $observer->getEvent();
		$userid = $event->getUser()->getId();		

		$locale = $this->_loadLocale($userid);
		if($locale == "")
		{
			$locale = $this->getDefaultLocale();
			$ret = $this->addNewRecord($userid, $locale);
		}
		
		//set Locale into adminhtml
		Mage::getSingleton('adminhtml/session')->setLocale($locale);
	}

	public function saveLocaleChange($observer)
	{
		$event = $observer->getEvent();
		$userid = $event->getData('userid');
		$locale = $event->getData('locale');

		$this->updateLocale($userid, $locale);
	}

	private function _loadLocale($userid)
	{
		try
		{
			$loc = Mage::getModel('ubl/ubl')->load($userid);
			$locale = $loc?$loc->getData('locale'):"";
						
		}
		catch(Exception $e)
		{
			//Add log tp Magento Log system
			$locale = $this->getDefaultLocale();
		}

		return $locale;
	}

	private function getDefaultLocale()
	{
		return Mage::getModel('ubl/ubl')->getDefaultLocale();
	}

	private function addNewRecord($userid, $locale_id)
	{
		try
		{
			$model = Mage::getModel('ubl/ubl');
			$model->setData('user_id', $userid);
			$model->setDate('locale', $locale_id);
			$model->save();
		}
		catch(Exception $e)
		{
			return false;
		}
		
		return true;
	}

	private function updateLocale($userid, $locale_id)
	{
		try
		{
			Mage::getModel('ubl/ubl')
				->load($userid)
				->setData('locale',$locale_id)
				->save();
		}
		catch(Exception $e)
		{
			return false;
		}

		return true;
	}

}
