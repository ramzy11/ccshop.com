<?php

class Gri_Page_Block_Html_Head extends Mage_Page_Block_Html_Head
{
    protected $_metaProperties = array();

    public function addMetaProperty($property, $content)
    {
        /* @var $helper Mage_Cms_Helper_Data */
        $helper = Mage::helper('cms');
        $content = $helper->getBlockTemplateProcessor()->filter($content);
        $this->_metaProperties[$property] = $content;
        return $this;
    }

    public function getMetaProperties()
    {
        return $this->_metaProperties;
    }

    public function getTitlePrefix()
    {
        return $this->getData('title_prefix') === NULL ?
            Mage::getStoreConfig('design/head/title_prefix') : $this->getData('title_prefix');
    }

    public function getTitleSuffix()
    {
        return $this->getData('title_suffix') === NULL ?
            Mage::getStoreConfig('design/head/title_suffix') : $this->getData('title_suffix');
    }

    public function setTitle($title)
    {
        $this->_data['title'] = $this->getTitlePrefix() . ' ' . $title
            . ' ' . $this->getTitleSuffix();
        return $this;
    }

    public function setTitleFromRegistry($registry)
    {
        return $this->setTitle(Mage::registry($registry)->getTitle());
    }
}
