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
 * Increment model
 *
 * Description:
 * For example we operate with such entities page, version and revision.
 * We store increments for version and revision in such way for
 * each page we need separate scope of version.
 * In all version we need separate scope for revisions.
 *
 * When we store counter for version it has node = page_id and level = 0
 * When we store counter for revision it has node = version_id (not increment number) and level = 1
 * In case we will add something after revision something like sub-revision
 * we will need to use node = revision_id and level = 2  (for future).
 * Type is only one value '0' at this time bc revision control used only for pages.
 *
 * @method Gri_Cms_Model_Resource_Increment _getResource()
 * @method Gri_Cms_Model_Resource_Increment getResource()
 * @method int getType()
 * @method Gri_Cms_Model_Increment setType(int $value)
 * @method int getNode()
 * @method Gri_Cms_Model_Increment setNode(int $value)
 * @method int getLevel()
 * @method Gri_Cms_Model_Increment setLevel(int $value)
 * @method int getLastId()
 * @method Gri_Cms_Model_Increment setLastId(int $value)
 *
 * @category    Gri
 * @package     Gri_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Gri_Cms_Model_Increment extends Mage_Core_Model_Abstract
{
    /*
     * Increment types
     */
    const TYPE_PAGE = 0;

    /*
     * Increment levels
     */
    const LEVEL_VERSION = 0;
    const LEVEL_REVISION = 1;

    /**
     * Constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('gri_cms/increment');
    }

    /**
     * Init mapping array of short fields to
     * its full names
     *
     * @resturn Varien_Object
     */
    protected function _initOldFieldsMap()
    {
        $this->_oldFieldsMap = array(
            'type'  => 'type',
            'node'  => 'node',
            'level' => 'level'
        );
        /*
         * $this->_oldFieldsMap = array(
            'type'  => 'increment_type',
            'node'  => 'increment_node',
            'level' => 'increment_level'
        );
         */
    }

    /**
     * Load increment counter by passed node and level
     *
     * @param int $type
     * @param int $node
     * @param int $level
     * @return Gri_Cms_Model_Increment
     */
    public function loadByTypeNodeLevel($type, $node, $level)
    {
        $this->getResource()->loadByTypeNodeLevel($this, $type, $node, $level);

        return $this;
    }

    /**
     * Get incremented value of counter.
     *
     * @return mixed
     */
    protected function _getNextId()
    {
        $incrementId = $this->getLastId();
        if ($incrementId) {
            $incrementId++;
        } else {
            $incrementId = 1;
        }

        return $incrementId;
    }

    /**
     * Generate new increment id for passed type, node and level.
     *
     * @param int $type
     * @param int $node
     * @param int $level
     * @return string
     */
    public function getNewIncrementId($type, $node, $level)
    {
        $this->loadByTypeNodeLevel($type, $node, $level);

        // if no counter for such combination we need to create new
        if (!$this->getId()) {
            $this->setType($type)
                ->setNode($node)
                ->setLevel($level);
        }

        $newIncrementId = $this->_getNextId();
        $this->setLastId($newIncrementId)->save();

        return $newIncrementId;
    }
}
