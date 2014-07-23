<?php

class Gri_Core_Model_Store extends Mage_Core_Model_Store
{

    public function getSession()
    {
        return $this->_getSession();
    }
}