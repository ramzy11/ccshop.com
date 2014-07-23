<?php

class Gri_Cms_Helper_Content extends Mage_Core_Helper_Abstract
{
    protected $_blockFields = array(
        'title',
        'identifier',
        'content',
        'is_active',
    );
    protected $_localeFiles = array(
        'en_US' => 'Eng',
        'fr_FR' => 'Fra',
        'ja_JP' => 'Jap',
        'zh_CN' => 'Chs',
        'zh_HK' => 'Cht',
    );
    protected $_pageFields = array(
        'title',
        'root_template',
        'meta_keywords',
        'meta_description',
        'identifier',
        'content_heading',
        'content',
        'is_active',
        'layout_update_xml',
        'under_version_control',
    );

    public function exportStoreBlocks(Mage_Core_Model_Store $store)
    {
        /* @var $collection Mage_Cms_Model_Resource_Block_Collection */
        $collection = Mage::getModel('cms/block')->getCollection();
        $collection->addStoreFilter($store)->setOrder('identifier', 'asc');
        $result = array();
        $blockFields = array_flip($this->_blockFields);
        /* @var $block Mage_Cms_Model_Block */
        foreach ($collection as $block) {
            $result[$block->getIdentifier()] = array_intersect_key($block->getData(), $blockFields);
        }
        file_put_contents($this->getContentByStore($store, 'block', 'path'), '<?php return ' . var_export($result, TRUE) . ';');
    }

    public function exportStorePages(Mage_Core_Model_Store $store)
    {
        /* @var $collection Mage_Cms_Model_Resource_Page_Collection */
        $collection = Mage::getModel('cms/page')->getCollection();
        $collection->addStoreFilter($store)->setOrder('identifier', 'asc');
        $result = array();
        $pageFields = array_flip($this->_pageFields);
        /* @var $page Mage_Cms_Model_Page */
        foreach ($collection as $page) {
            $result[$page->getIdentifier()] = array_intersect_key($page->getData(), $pageFields);
        }
        file_put_contents($this->getContentByStore($store, 'page', 'path'), '<?php return ' . var_export($result, TRUE) . ';');
    }

    public function getContentByStore(Mage_Core_Model_Store $store, $contentType = 'page', $return = 'content')
    {
        $contentType = strtolower($contentType) == 'page' ? 'Page' : 'Block';
        $contentPath = dirname(__FILE__) . DS . 'Content' . DS . $contentType;
        $localeCode = Mage::getStoreConfig('general/locale/code', $store->getId());
        $contentPath .= isset($this->_localeFiles[$localeCode]) ?
            $this->_localeFiles[$localeCode] : reset($this->_localeFiles);
        $contentPath .= '.php';
        return $return == 'content' ? (is_file($contentPath) ? include($contentPath) : array()) : $contentPath;
    }

    public function updateStoreBlocks(Mage_Core_Model_Store $store, array $identifiers)
    {
        $blocks = $this->getContentByStore($store, 'block');
        /* @var $block Mage_Cms_Model_Block */
        $block = Mage::getModel('cms/block');
        $storeId = $store->getId();
        /* @var $transactionSave Mage_Core_Model_Resource_Transaction */
        $transactionSave = Mage::getModel('core/resource_transaction');
        foreach ($identifiers as $identifier) {
            if (!isset($blocks[$identifier])) continue;
            $block->unsetData();
            $block->setStoreId($storeId)->setLoadInactive(TRUE)->load($blocks[$identifier]['identifier']);
            foreach ($blocks[$identifier] as $k => $v) {
                $block->setData($k, $v);
            }
            $block->setStores(array($storeId));
            $transactionSave->addObject(clone $block);
        }
        $transactionSave->save();
    }

    public function updateStorePages(Mage_Core_Model_Store $store, array $identifiers)
    {
        $pages = $this->getContentByStore($store, 'page');
        /* @var $page Mage_Cms_Model_Page */
        $page = Mage::getModel('cms/page');
        $storeId = $store->getId();
        /* @var $transactionSave Mage_Core_Model_Resource_Transaction */
        $transactionSave = Mage::getModel('core/resource_transaction');
        foreach ($identifiers as $identifier) {
            if (!isset($pages[$identifier])) continue;
            $page->unsetData();
            $page->setStoreId($storeId)->load($pages[$identifier]['identifier']);
            foreach ($pages[$identifier] as $k => $v) {
                $page->setData($k, $v);
            }
            $page->setStores(array($storeId));
            $transactionSave->addObject(clone $page);
        }
        $transactionSave->save();
    }
}
