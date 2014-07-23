<?php

class Gri_ColorFilter_Model_ColorFilter extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init('gri_colorfilter/colorFilter');
    }

    public function loadActiveColor()
    {
        $this->load($this->getResource()->getActiveColorId());
        return $this;
    }

    protected function _beforeSave()
    {
        if (is_array($image = $this->getImage())) {
            isset($image['value']) and $this->setImage($image['value']);
            if (isset($image['delete']) && $image['delete']) {
                $this->setImage('');
                is_file($filename = $this->getHelper()->getImagePath($this->getOrigData('image'))) and unlink($filename);
            }
        }

        return parent::_beforeSave();
    }

}
