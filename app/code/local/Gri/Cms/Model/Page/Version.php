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
 * Cms page version model
 *
 * @method Gri_Cms_Model_Resource_Page_Version _getResource()
 * @method Gri_Cms_Model_Resource_Page_Version getResource()
 * @method string getLabel()
 * @method Gri_Cms_Model_Page_Version setLabel(string $value)
 * @method string getAccessLevel()
 * @method Gri_Cms_Model_Page_Version setAccessLevel(string $value)
 * @method int getPageId()
 * @method Gri_Cms_Model_Page_Version setPageId(int $value)
 * @method int getUserId()
 * @method Gri_Cms_Model_Page_Version setUserId(int $value)
 * @method int getRevisionsCount()
 * @method Gri_Cms_Model_Page_Version setRevisionsCount(int $value)
 * @method int getVersionNumber()
 * @method Gri_Cms_Model_Page_Version setVersionNumber(int $value)
 * @method string getCreatedAt()
 * @method Gri_Cms_Model_Page_Version setCreatedAt(string $value)
 *
 * @category    Gri
 * @package     Gri_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Gri_Cms_Model_Page_Version extends Mage_Core_Model_Abstract
{
    /**
     * Prefix of model events names.
     *
     * @var string
     */
    protected $_eventPrefix = 'gri_cms_version';

    /**
     * Parameter name in event.
     * In observe method you can use $observer->getEvent()->getObject() in this case.
     *
     * @var string
     */
    protected $_eventObject = 'version';

    /**
     * Access level constants
     */
    const ACCESS_LEVEL_PRIVATE = 'private';
    const ACCESS_LEVEL_PROTECTED = 'protected';
    const ACCESS_LEVEL_PUBLIC = 'public';

    /**
     * Constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('gri_cms/page_version');
    }

    /**
     * Preparing data before save
     *
     * @return Gri_Cms_Model_Page_Version
     */
    protected function _beforeSave()
    {
        if (!$this->getId()) {
            $incrementNumber = Mage::getModel('gri_cms/increment')
                ->getNewIncrementId(Gri_Cms_Model_Increment::TYPE_PAGE,
                        $this->getPageId(), Gri_Cms_Model_Increment::LEVEL_VERSION);

            $this->setVersionNumber($incrementNumber);
            $this->setCreatedAt(Mage::getSingleton('core/date')->gmtDate());
        }

        if (!$this->getLabel()) {
            Mage::throwException(Mage::helper('gri_cms')->__('Label for version is a required field.'));
        }

        // We cannot allow changing access level for some versions
        if ($this->getAccessLevel() != $this->getOrigData('access_level')) {
            if ($this->getOrigData('access_level') == Gri_Cms_Model_Page_Version::ACCESS_LEVEL_PUBLIC) {
                $resource = $this->_getResource();
                /* @var $resource Gri_Cms_Model_Mysql4_Page_Version */

                if ($resource->isVersionLastPublic($this)) {
                    Mage::throwException(
                        Mage::helper('gri_cms')->__('Cannot change version access level because it is the last public version for its page.')
                    );
                }

//                if ($resource->isVersionHasPublishedRevision($this)) {
//                    Mage::throwException(
//                        Mage::helper('gri_cms')->__('Cannot change version access level because its revision is published.')
//                    );
//                }
            }
        }

        return parent::_beforeSave();
    }

    /**
     * Processing some data after version saved
     *
     * @return Gri_Cms_Model_Page_Version
     */
    protected function _afterSave()
    {
        // If this was a new version we should create initial revision for it
        // from specified revision or from latest for parent version
        if ($this->getOrigData($this->getIdFieldName()) != $this->getId()) {
            $revision = Mage::getModel('gri_cms/page_revision');

            // setting data for load
            $userId = $this->getUserId();
            $accessLevel = Mage::getSingleton('gri_cms/config')->getAllowedAccessLevel();

            if ($this->getInitialRevisionData()) {
                $revision->setData($this->getInitialRevisionData());
            } else {
                $revision->loadWithRestrictions($accessLevel, $userId, $this->getOrigData($this->getIdFieldName()), 'version_id');
            }

            $revision->setVersionId($this->getId())
                ->setUserId($userId)
                ->save();

            $this->setLastRevision($revision);
        }
        return parent::_afterSave();
    }

    /**
     * Checking some moments before we can actually delete version
     *
     * @return Gri_Cms_Model_Page_Version
     */
    protected function _beforeDelete()
    {
        $resource = $this->_getResource();
        /* @var $resource Gri_Cms_Model_Mysql4_Page_Version */
        if ($this->isPublic()) {
            if ($resource->isVersionLastPublic($this)) {
                Mage::throwException(
                    Mage::helper('gri_cms')->__('Version "%s" could not be removed because it is the last public version for its page.', $this->getLabel())
                );
            }
        }

        if ($resource->isVersionHasPublishedRevision($this)) {
            Mage::throwException(
                Mage::helper('gri_cms')->__('Version "%s" could not be removed because its revision has been published.', $this->getLabel())
            );
        }

        return parent::_beforeDelete();
    }

    /**
     * Removing unneeded data from increment table after version was removed.
     *
     * @param $observer
     * @return Gri_Cms_Model_Observer
     */
    protected function _afterDelete()
    {
        Mage::getResourceSingleton('gri_cms/increment')
            ->cleanIncrementRecord(Gri_Cms_Model_Increment::TYPE_PAGE,
                $this->getId(),
                Gri_Cms_Model_Increment::LEVEL_REVISION);

        return parent::_afterDelete();
    }

    /**
     * Check if this version public or not.
     *
     * @return bool
     */
    public function isPublic()
    {
        return $this->getAccessLevel() == Gri_Cms_Model_Page_Version::ACCESS_LEVEL_PUBLIC;
    }

    /**
     * Loading version with extra access level checking.
     *
     * @param array|string $accessLevel
     * @param int $userId
     * @param int|string $value
     * @param string|null $field
     * @return Gri_Cms_Model_Page_Version
     */
    public function loadWithRestrictions($accessLevel, $userId, $value, $field = null)
    {
        $this->_getResource()->loadWithRestrictions($this, $accessLevel, $userId, $value, $field = null);
        $this->_afterLoad();
        $this->setOrigData();
        return $this;
    }
}
