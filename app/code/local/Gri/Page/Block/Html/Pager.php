<?php
class Gri_Page_Block_Html_Pager extends Mage_Page_Block_Html_Pager
{
	public function getPagerUrl($params=array())
	{
		$params['isAjax'] = NULL;
		$urlParams = array();
		$urlParams['_current']  = true;
		$urlParams['_escape']   = true;
		$urlParams['_use_rewrite']   = true;
		$urlParams['_query']    = $params;
		return $this->getUrl('*/*/*', $urlParams);
	}
}