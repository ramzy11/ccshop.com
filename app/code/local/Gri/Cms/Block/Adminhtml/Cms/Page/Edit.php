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
 * Cms page edit form revisions tab
 *
 * @category    Gri
 * @package     Gri_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Gri_Cms_Block_Adminhtml_Cms_Page_Edit
    extends Mage_Adminhtml_Block_Template
{
    /**
     * Adding js to CE blocks to implement special functionality which
     * will allow go back to edit page with pre loaded tab passed through query string.
     * Added permission checking to remove some buttons if needed.
     *
     * @return Gri_Cms_Block_Adminhtml_Cms_Page_Edit
     */
    protected function _prepareLayout()
    {
        $tabsBlock = $this->getLayout()->getBlock('cms_page_edit_tabs');
        /* @var $tabBlock Mage_Adminhtml_Block_Cms_Page_Edit_Tabs */
        if ($tabsBlock) {
            $editBlock = $this->getLayout()->getBlock('cms_page_edit');
            /* @var $editBlock Mage_Adminhtml_Block_Cms_Page_Edit */
            if ($editBlock) {
                $page = Mage::registry('cms_page');
                if ($page) {
                    if ($page->getId()) {
                        $editBlock->addButton('preview', array(
                            'label'     => Mage::helper('gri_cms')->__('Preview'),
                            'onclick'   => 'pagePreviewAction()',
                            'class'     => 'preview',
                        ));
                    }

                    $formBlock = $editBlock->getChild('form');
                    if ($formBlock) {
                        $formBlock->setTemplate('gri/cms/page/edit/form.phtml');
                        if ($page->getUnderVersionControl()) {
                            $tabId = $this->getRequest()->getParam('tab');
                            if ($tabId) {
                                $formBlock->setSelectedTabId($tabsBlock->getId() . '_' . $tabId)
                                    ->setTabJsObject($tabsBlock->getJsObjectName());
                            }
                        }
                    }
                    // If user non-publisher he can save page only if it has disabled status
                    if ($page->getUnderVersionControl()) {
                        if ($page->getId() && $page->getIsActive() == Mage_Cms_Model_Page::STATUS_ENABLED) {
                            if (!Mage::getSingleton('gri_cms/config')->canCurrentUserPublishRevision()) {
                                $editBlock->removeButton('delete');
                                $editBlock->removeButton('save');
                                $editBlock->removeButton('saveandcontinue');
                            }
                        }
                    }
                }
            }
        }
        return $this;
    }
}
