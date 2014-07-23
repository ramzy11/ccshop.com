<?php

class Gri_Core_Model_Admin_Observer extends Mage_Admin_Model_Observer
{

    public function actionPreDispatchAdmin($observer)
    {
        if (parent::actionPreDispatchAdmin($observer) === FALSE) {
            $request = Mage::app()->getRequest();
            $request->setRouteName('adminhtml');
            return FALSE;
        }
    }
}
