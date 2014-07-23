<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
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
 * @package     Gri_GiftCard
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

class Gri_GiftCard_Block_Adminhtml_Catalog_Product_Edit_Tab_Giftcard
 extends Mage_Adminhtml_Block_Widget
 implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    protected function _construct()
    {
        $this->setTemplate('gri/giftcard/catalog/product/edit/tab/giftcard.phtml');
        parent::_construct();
    }
    /**
     * Get tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('gri_giftcard')->__('Gift Card Information');
    }

    /**
     * Get tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('gri_giftcard')->__('Gift Card Information');
    }

    /**
     * Check if tab can be displayed
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Check if tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Return true when current product is new
     *
     * @return bool
     */
    public function isNew()
    {
        if (Mage::registry('product')->getId()) {
            return false;
        }
        return true;
    }

    /**
     * Return field name prefix
     *
     * @return string
     */
    public function getFieldPrefix()
    {
        return 'product';
    }

    /**
     * Get current product value, when no value available - return config value
     *
     * @param string $field
     * @return string
     */
    public function getFieldValue($field)
    {
        if (!$this->isNew()) {
            return Mage::registry('product')->getDataUsingMethod($field);
        }

        return Mage::getStoreConfig(Gri_GiftCard_Model_Giftcard::XML_PATH . $field);
    }

    /**
     * Return gift card types
     *
     * @return array
     */
    public function getCardTypes()
    {
        return array(
            Gri_GiftCard_Model_Giftcard::TYPE_VIRTUAL  => Mage::helper('gri_giftcard')->__('Virtual'),
            Gri_GiftCard_Model_Giftcard::TYPE_PHYSICAL => Mage::helper('gri_giftcard')->__('Physical'),
            Gri_GiftCard_Model_Giftcard::TYPE_COMBINED => Mage::helper('gri_giftcard')->__('Combined'),
        );
    }

    /**
     * Return email template select options
     *
     * @return array
     */
    public function getEmailTemplates()
    {
        $result = array();
        $template = Mage::getModel('adminhtml/system_config_source_email_template');
        $template->setPath(Gri_GiftCard_Model_Giftcard::XML_PATH_EMAIL_TEMPLATE);
        foreach ($template->toOptionArray() as $one) {
            $result[$one['value']] = $one['label'];
        }
        return $result;
    }

    public function getConfigValue($field)
    {
        return Mage::getStoreConfig(Gri_GiftCard_Model_Giftcard::XML_PATH . $field);
    }

    /**
     * Check block is readonly
     *
     * @return boolean
     */
    public function isReadonly()
    {
        return Mage::registry('product')->getGiftCardReadonly();
    }
}
