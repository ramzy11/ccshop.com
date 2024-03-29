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
 * Cms page version resource model
 *
 * @category    Gri
 * @package     Gri_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Gri_Cms_Model_Resource_Page_Version extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('gri_cms/page_version', 'version_id');
    }

    /**
     * Checking if version id not last public for its page
     *
     * @param Mage_Core_Model_Abstract $object
     * @return bool
     */
    public function isVersionLastPublic(Mage_Core_Model_Abstract $object)
    {
        $select = $this->_getReadAdapter()->select();
        $select->from($this->getMainTable(), 'COUNT(*)')
            ->where(implode(' AND ', array(
                'page_id      = :page_id',
                'access_level = :access_level',
                'version_id   = :version_id'
            )));

        $bind = array(
            ':page_id'      => $object->getPageId(),
            ':access_level' => Gri_Cms_Model_Page_Version::ACCESS_LEVEL_PUBLIC,
            ':version_id'   => $object->getVersionId()
        );

        return !$this->_getReadAdapter()->fetchOne($select, $bind);
    }

    /**
     * Checking if Version does not contain published revision
     *
     * @param Mage_Core_Model_Abstract $object
     * @return bool
     */
    public function isVersionHasPublishedRevision(Mage_Core_Model_Abstract $object)
    {
        $select = $this->_getReadAdapter()->select();
        $select->from(array('p' => $this->getTable('cms/page')), array())
            ->where('p.page_id = ?', (int)$object->getPageId())
            ->join(
                array('r' => $this->getTable('gri_cms/page_revision')),
                'r.revision_id = p.published_revision_id',
                'r.version_id');

        $result = $this->_getReadAdapter()->fetchOne($select);

        return $result == $object->getVersionId();
    }

    /**
     * Add access restriction filters to allow load only by granted user.
     *
     * @param Varien_Db_Select $select
     * @param int $accessLevel
     * @param int $userId
     * @return Varien_Db_Select
     */
    protected function _addAccessRestrictionsToSelect($select, $accessLevel, $userId)
    {
        $conditions = array();

        $conditions[] = $this->_getReadAdapter()->quoteInto('user_id = ?', (int)$userId);

        if (!empty($accessLevel)) {
            if (!is_array($accessLevel)) {
                $accessLevel = array($accessLevel);
            }
            $conditions[] = $this->_getReadAdapter()->quoteInto('access_level IN (?)', $accessLevel);
        } else {
            $conditions[] = 'access_level IS NULL';
        }

        $select->where(implode(' OR ', $conditions));

        return $select;
    }

    /**
     * Loading data with extra access level checking.
     *
     * @param Gri_Cms_Model_Page_Version $object
     * @param array|string $accessLevel
     * @param int $userId
     * @param int|string $value
     * @param string|null $field
     * @return Gri_Cms_Model_Resource_Page_Version
     */
    public function loadWithRestrictions($object, $accessLevel, $userId, $value, $field = null)
    {
        if ($field === null) {
            $field = $this->getIdFieldName();
        }

        $read = $this->_getReadAdapter();
        if ($value) {
            $select = $this->_getLoadSelect($field, $value, $object);
            $select = $this->_addAccessRestrictionsToSelect($select, $accessLevel, $userId);
            $data   = $read->fetchRow($select);
            if ($data) {
                $object->setData($data);
            }
        }

        $this->_afterLoad($object);
        return $this;
    }
}
