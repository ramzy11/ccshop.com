<?php

/**
 * @method Mage_Cms_Block_Block getCmsBlock()
 */
class Gri_Newcomer_Block_Popup extends Mage_Core_Block_Template
{

    /**
     * @return Gri_Newcomer_Helper_Data
     */
    protected function _getHelper()
    {
        return $this->helper('newcomer');
    }

    protected function _toHtml()
    {
        $html = '';
        if ($this->_getHelper()->isEnabled()) {
            $html = parent::_toHtml();
        }
        return $html;
    }

    public function getCookieName()
    {
        return Gri_Newcomer_Helper_Data::COOKIE_PATH_POPUP_POPPED;
    }

    public function getPopupContent()
    {
        if ($this->getData($key = 'popup_content') === NULL) {
            $this->setCmsBlock($this->getLayout()->createBlock('cms/block'));
            $content = $this->getCmsBlock()
                ->setBlockId(Mage::getStoreConfig(Gri_Newcomer_Helper_Data::CONFIG_PATH_POPUP_BLOCK))
                ->toHtml();
            $this->setData($key, $content);
        }
        return $this->getData($key);
    }
}
