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
        $mapping = array('grey'=>'grey','light grey'=>'grey','light_grey'=>'grey','black'=>'platinum');

        return isset($mapping[strtolower($level)])?$mapping[strtolower($level)]:$level;
    }

    private function getAPIUrl($method="api", $isDev=0)
    {
        return self::API_DOMAIN.':'.self::API_PORT.'/'.$method;
    }

    public function checkVipAccount($data=array())
    {
        $method = 'VipAdd';
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
		Mage::Log('posting '.$data['data'].' to '.$url,7,'gri-debug.log');
		Mage::Log('result :'.$tmp,7,'gri-debug.log');
        curl_close($c);
        return json_decode($tmp, true);
    }

    private function getAs400CountryMapping()
    {
        return array('HK'=>'HKG','MY'=>'MAL','SG'=>'SIN','TW'=>'TWN','TH'=>'THA','MO'=>'MAC','CN'=>'CHN','JP'=>'JPN');
    }

    public function createAddArray(Mage_Customer_model_Customer $customer)
    {
        $countryMapping = $this->getAs400CountryMapping();
        $data = array();

		$expiry_date = '20991231';
        $email = $customer->getEmail();
        $expiry_date = '20991231';
        $firstname = $customer->getFirstname();
        $surname = $customer->getLastname();
        $salulation = strtoupper($customer->getTitle());
        $grade = 'GREY';
        $vip_country = 'HKG';
        $dob = $customer->getDob();
        $mobile = $customer->getMobile();
        $cardno = "0";
        $district = $customer->getCustomerAreaCode();
        $country = isset($countryMapping[$customer->getCustomerCountryId()])?$countryMapping[$customer->getCustomerCountryId()]:'HKG';
        $join_date = date("Ymd");
        //$amend_user = $amend_date = $amend_time = "";
        $currentVipPoint = ".00";
        $age_group = "0";
        $gender = ($salulation == 'MR'?'M':'F');
        $address = $customer->getMailingAddress();

        //$data = compact('vip_country','expiry_date','cardno','grade','salulation','firstname','surname','gender','mobile','country','age_group','birthday','other_phone','email','address','join_date','amend_user','amend_date','amend_time',);
        $data = compact('vip_country','expiry_date','cardno','grade','salulation','firstname','surname','gender','mobile','country','age_group','birthday','other_phone','email','join_date','address');

        $data['pk#'] = '0';
        //$data['current vip point'] = $currentVipPoint;
        $data['district#'] = $district;
        $data['birthday'] = '(DD)'.substr($dob,8,2).'(MM)'.substr($dob,5,2).'(YYYY)0';

        return $data;
    }

} 