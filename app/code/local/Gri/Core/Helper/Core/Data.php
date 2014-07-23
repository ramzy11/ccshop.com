<?php

class Gri_Core_Helper_Core_Data extends Mage_Core_Helper_Data
{
    protected $_canEscapeHtml = TRUE;

    public function escapeHtml($data, $allowedTags = null)
    {
        if ($this->_canEscapeHtml) $data = parent::escapeHtml($data, $allowedTags);
        return $data;
    }

    public function setCanEscapeHtml($canEscapeHtml = TRUE)
    {
        $this->_canEscapeHtml = $canEscapeHtml;
        return $this;
    }
}
