<?php
/**
 * Magento Gri Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Gri Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/gri-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Gri
 * @package     Gri_Cms
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/gri-edition
 */


/**
 * Cms page revision model
 *
 * @method Gri_Cms_Model_Resource_Page_Revision _getResource()
 * @method Gri_Cms_Model_Resource_Page_Revision getResource()
 * @method int getVersionId()
 * @method Gri_Cms_Model_Page_Revision setVersionId(int $value)
 * @method int getPageId()
 * @method Gri_Cms_Model_Page_Revision setPageId(int $value)
 * @method string getRootTemplate()
 * @method Gri_Cms_Model_Page_Revision setRootTemplate(string $value)
 * @method string getMetaKeywords()
 * @method Gri_Cms_Model_Page_Revision setMetaKeywords(string $value)
 * @method string getMetaDescription()
 * @method Gri_Cms_Model_Page_Revision setMetaDescription(string $value)
 * @method string getContentHeading()
 * @method Gri_Cms_Model_Page_Revision setContentHeading(string $value)
 * @method string getContent()
 * @method Gri_Cms_Model_Page_Revision setContent(string $value)
 * @method string getCreatedAt()
 * @method Gri_Cms_Model_Page_Revision setCreatedAt(string $value)
 * @method string getLayoutUpdateXml()
 * @method Gri_Cms_Model_Page_Revision setLayoutUpdateXml(string $value)
 * @method string getCustomTheme()
 * @method Gri_Cms_Model_Page_Revision setCustomTheme(string $value)
 * @method string getCustomRootTemplate()
 * @method Gri_Cms_Model_Page_Revision setCustomRootTemplate(string $value)
 * @method string getCustomLayoutUpdateXml()
 * @method Gri_Cms_Model_Page_Revision setCustomLayoutUpdateXml(string $value)
 * @method string getCustomThemeFrom()
 * @method Gri_Cms_Model_Page_Revision setCustomThemeFrom(string $value)
 * @method string getCustomThemeTo()
 * @method Gri_Cms_Model_Page_Revision setCustomThemeTo(string $value)
 * @method int getUserId()
 * @method Gri_Cms_Model_Page_Revision setUserId(int $value)
 * @method int getRevisionNumber()
 * @method Gri_Cms_Model_Page_Revision setRevisionNumber(int $value)
 *
 * @category    Gri
 * @package     Gri_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Gri_Cms_Model_Page_Revision extends Mage_Core_Model_Abstract
{
    /**
     * Prefix of model events names.
     *
     * @var string
     */
    protected $_eventPrefix = 'gri_cms_revision';

    /**
     * Parameter name in event.
     * In observe method you can use $observer->getEvent()->getObject() in this case.
     *
     * @var string
     */
    protected $_eventObject = 'revision';

    /**
     * Configuration model
     * @var Gri_Cms_Model_Config
     */
    protected $_config;

    protected $_cacheTag = 'CMS_REVISION';

    /**
     * Constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('gri_cms/page_revision');
        $this->_config = Mage::getSingleton('gri_cms/config');
    }

    /**
     * Get cahce tags associated with object id
     *
     * @return array
     */
    public function getCacheIdTags()
    {
        $tags = parent::getCacheIdTags();
        if ($tags && $this->getPageId()) {
            $tags[] = Mage_Cms_Model_Page::CACHE_TAG.'_'.$this->getPageId();
        }
        return $tags;
    }

    /**
     * Preparing data before save
     *
     * @return Gri_Cms_Model_Page_Revision
     */
    protected function _beforeSave()
    {
        /*
         * Reseting revision id this revision should be saved as new.
         * Bc data was changed or original version id not equals to new version id.
         */
        if ($this->_revisionedDataWasModified() || $this->getVersionId() != $this->getOrigData('version_id')) {
            $this->unsetData($this->getIdFieldName());
            $this->setCreatedAt(Mage::getSingleton('core/date')->gmtDate());

            $incrementNumber = Mage::getModel('gri_cms/increment')
                ->getNewIncrementId(Gri_Cms_Model_Increment::TYPE_PAGE,
                        $this->getVersionId(), Gri_Cms_Model_Increment::LEVEL_REVISION);

            $this->setRevisionNumber($incrementNumber);
        }

        return parent::_beforeSave();
    }

    /**
     * Check data which is under revision control if it was modified.
     *
     * @return array
     */
    protected function _revisionedDataWasModified()
    {
        $attributes = $this->_config->getPageRevisionControledAttributes();
        foreach ($attributes as $attr) {
            $value = $this->getData($attr);
            if ($this->getOrigData($attr) !== $value) {
                if ($this->getOrigData($attr) === NULL && $value === '' || $value === NULL) {
                    continue;
                }
                return true;
            }
        }
        return false;
    }

    /**
     * Prepare data which must be published
     *
     * @return array
     */
    protected function _prepareDataForPublish()
    {
        $data = array();
        $attributes = $this->_config->getPageRevisionControledAttributes();
        foreach ($this->getData() as $key => $value) {
            if (in_array($key, $attributes)) {
                $this->unsetData($key);
                $data[$key] = $value;
            }
        }

        $data['published_revision_id'] = $this->getId();

        return $data;
    }

    /**
     * Publishing current revision
     *
     * @return Gri_Cms_Model_Page_Revision
     */
    public function publish()
    {
        $this->_getResource()->beginTransaction();
        try {
            $data = $this->_prepareDataForPublish($this);
            $object = Mage::getModel('gri_cms/page_revision')->setData($data);
            $this->_getResource()->publish($object, $this->getPageId());
            $this->_getResource()->commit();
        } catch (Exception $e){
            $this->_getResource()->rollBack();
            throw $e;
        }
        $this->cleanModelCache();
        return $this;
    }

    /**
     * Checking some moments before we can actually delete revision
     *
     * @return Gri_Cms_Model_Page_Revision
     */
    protected function _beforeDelete()
    {
        $resource = $this->_getResource();
        /* @var $resource Gri_Cms_Model_Mysql4_Page_Revision */
        if ($resource->isRevisionPublished($this)) {
            Mage::throwException(
                Mage::helper('gri_cms')->__('Revision #%s could not be removed because it is published.', $this->getRevisionNumber())
            );
        }
    }

    /**
     * Loading revision with extra access level checking.
     *
     * @param array|string $accessLevel
     * @param int $userId
     * @param int|string $value
     * @param string|null $field
     * @return Gri_Cms_Model_Page_Revision
     */
    public function loadWithRestrictions($accessLevel, $userId, $value, $field = null)
    {
        $this->_getResource()->loadWithRestrictions($this, $accessLevel, $userId, $value, $field);
        $this->_afterLoad();
        $this->setOrigData();
        return $this;
    }

    /**
     * Loading revision with empty data which is under
     * control and with other data from version and page.
     * Also apply extra access level checking.
     *
     * @param int $versionId
     * @param int $pageId
     * @param array|string $accessLevel
     * @param int $userId
     * @return Gri_Cms_Model_Page_Revision
     */
    public function loadByVersionPageWithRestrictions($versionId, $pageId, $accessLevel, $userId)
    {
        $this->_getResource()->loadByVersionPageWithRestrictions($this, $versionId, $pageId, $accessLevel, $userId);
        $this->_afterLoad();
        $this->setOrigData();
        return $this;
    }
}
