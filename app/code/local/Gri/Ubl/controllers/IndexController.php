<?php

class Gri_Ubl_IndexController extends Mage_Core_Controller_Front_Action
{
	public function indexAction()
	{
		$model = Mage::getModel('ubl/ubl');
		$this->loadLayout();
		$this->renderLayout();
	}
}
