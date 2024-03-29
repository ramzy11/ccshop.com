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
 * Cms Hierarchy Head Block
 *
 * @category   Gri
 * @package    Gri_Cms
 */
class Gri_Cms_Block_Hierarchy_Head extends Mage_Core_Block_Abstract
{
    /**
     * Prepare Global Layout
     *
     * @return Gri_Cms_Block_Hieararchy_Head
     */
    protected function _prepareLayout()
    {
        /* @var $node Gri_Cms_Model_Hierarchy_Node */
        $node      = Mage::registry('current_cms_hierarchy_node');
        /* @var $head Mage_Page_Block_Html_Head */
        $head      = $this->getLayout()->getBlock('head');

        if (Mage::helper('gri_cms/hierarchy')->isMetadataEnabled() && $node && $head) {
            $treeMetaData = $node->getTreeMetaData();
            if (is_array($treeMetaData)) {
                /* @var $linkNode Gri_Cms_Model_Hierarchy_Node */

                if ($treeMetaData['meta_cs_enabled']) {
                    $linkNode = $node->getMetaNodeByType(Gri_Cms_Model_Hierarchy_Node::META_NODE_TYPE_CHAPTER);
                    if ($linkNode->getId()) {
                        $head->addLinkRel(Gri_Cms_Model_Hierarchy_Node::META_NODE_TYPE_CHAPTER, $linkNode->getUrl());
                    }

                    $linkNode = $node->getMetaNodeByType(Gri_Cms_Model_Hierarchy_Node::META_NODE_TYPE_SECTION);
                    if ($linkNode->getId()) {
                        $head->addLinkRel(Gri_Cms_Model_Hierarchy_Node::META_NODE_TYPE_SECTION, $linkNode->getUrl());
                    }
                }

                if ($treeMetaData['meta_next_previous']) {
                    $linkNode = $node->getMetaNodeByType(Gri_Cms_Model_Hierarchy_Node::META_NODE_TYPE_NEXT);
                    if ($linkNode->getId()) {
                        $head->addLinkRel(Gri_Cms_Model_Hierarchy_Node::META_NODE_TYPE_NEXT, $linkNode->getUrl());
                    }

                    $linkNode = $node->getMetaNodeByType(Gri_Cms_Model_Hierarchy_Node::META_NODE_TYPE_PREVIOUS);
                    if ($linkNode->getId()) {
                        $head->addLinkRel(Gri_Cms_Model_Hierarchy_Node::META_NODE_TYPE_PREVIOUS, $linkNode->getUrl());
                    }
                }

                if ($treeMetaData['meta_first_last']) {
                    $linkNode = $node->getMetaNodeByType(Gri_Cms_Model_Hierarchy_Node::META_NODE_TYPE_FIRST);
                    if ($linkNode->getId()) {
                        $head->addLinkRel(Gri_Cms_Model_Hierarchy_Node::META_NODE_TYPE_FIRST, $linkNode->getUrl());
                    }
                }
            }
        }

        return parent::_prepareLayout();
    }
}
