<?php

class Gri_Page_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getCmsBlockHtml($identifier){
        return Mage::app()->getLayout()->createBlock('cms/block')->setBlockId($identifier)->toHtml();
    }
}
