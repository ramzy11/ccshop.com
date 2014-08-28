<?php


class Gri_Vip_Model_Vip_As400 extends Mage_Core_Model_Abstract
{
	const API_DOMAIN = 'http://as400.griretail.com';
	const API_PORT = '62051';
	
	private $customerGroup;

	public function _construct()
	{
		$this->_init('gri_vip/vip_as400');
	}

	public function getCustomerGroup()
	{
		if(!$this->customerGroup)
		{
			$data = array();
			$groups = Mage::getSingleton('customer/group')->getCollection();
			foreach($groups as $group)
			{
				$data[strtolower($group->getCode())] = $group->getId();
			}

			$this->customerGroup = $data;
			unset($data);
			unset($groups);
		}	

		return $this->customerGroup;
	}

	public function offlineGradeMapping($level)
	{
		$mapping = array('light grey'=>'general','light_grey'=>'general','black'=>'platinum');
	
		return isset($mapping[strtolower($level)])?$mapping[strtolower($level)]:$level;
	}

	private function getAPIUrl($method="api")
	{
		return self::API_DOMAIN.':'.self::API_PORT.'/'.$method;
	}

	public function checkVipAccount($data=array())
	{
		$method = 'VipDetail';
		$json = $this->createJson($method, $data);
		$ret =	$this->callWebService($this->getAPIUrl('api'),array('data'=>$json));

		return $ret;
	}

	public function checkVipContent($vipPk = "")
	{
		$method = 'VipList';
		$json = $this->createJson($method, array('search1'=>$vipPk));
		$ret = $this->callWebService($this->getAPIUrl('api'), array('data'=>$json));
		
		return $ret;
	}

	public function checkVipInfo($vipPk = "")
	{
		$method = 'VipInfo';
		$json = $this->createJson($method, array('pk#'=>$vipPk));
		$ret = $this->callWebService($this->getAPIUrl('api'), array('data'=>$json));
		return $ret;
	}

	private function createJson($method="",$data=array())
	{
		$output = array();
		$output['method'] = $method;
		$output['data'] = $data;
		return json_encode($output);
	}

	private function callWebService($url, $data=array())
	{
		$c = curl_init($url);
		curl_setopt($c, CURLOPT_POST,1);
		curl_setopt($c, CURLOPT_POSTFIELDS,'data='.$data['data']);
		curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
		$tmp = curl_exec($c);
		curl_close($c);
		return json_decode($tmp, true);
	}
}
