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
 * @package     Gri_Reward
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/gri-edition
 */

/**
 * Customer account reward history block
 *
 * @category    Gri
 * @package     Gri_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Gri_Reward_Block_Customer_Reward_History extends Mage_Core_Block_Template
{
    /**
     * History records collection
     *
     * @var Gri_Reward_Model_Mysql4_Reward_History_Collection
     */
    protected $_collection = null;

    /**
     * Get history collection if needed
     *
     * @return Gri_Reward_Model_Mysql4_Reward_History_Collection|false
     */
    public function getHistory()
    {
        if (0 == $this->_getCollection()->getSize()) {
            return false;
        }
        return $this->_collection;
    }

    /**
     * History item points delta getter
     *
     * @param Gri_Reward_Model_Reward_History $item
     * @return string
     */
    public function getPointsDelta(Gri_Reward_Model_Reward_History $item)
    {
        return Mage::helper('gri_reward')->formatPointsDelta($item->getPointsDelta());
    }

    /**
     * History item points balance getter
     *
     * @param Gri_Reward_Model_Reward_History $item
     * @return string
     */
    public function getPointsBalance(Gri_Reward_Model_Reward_History $item)
    {
        return $item->getPointsBalance();
    }

    /**
     * History item currency balance getter
     *
     * @param Gri_Reward_Model_Reward_History $item
     * @return string
     */
    public function getCurrencyBalance(Gri_Reward_Model_Reward_History $item)
    {
        return Mage::helper('core')->currency($item->getCurrencyAmount());
    }

    /**
     * History item reference message getter
     *
     * @param Gri_Reward_Model_Reward_History $item
     * @return string
     */
    public function getMessage(Gri_Reward_Model_Reward_History $item)
    {
        return $item->getMessage();
    }

    /**
     * History item reference additional explanation getter
     *
     * @param Gri_Reward_Model_Reward_History $item
     * @return string
     */
    public function getExplanation(Gri_Reward_Model_Reward_History $item)
    {
        return ''; // TODO
    }

    /**
     * History item creation date getter
     *
     * @param Gri_Reward_Model_Reward_History $item
     * @return string
     */
    public function getDate(Gri_Reward_Model_Reward_History $item)
    {
        return Mage::helper('core')->formatDate($item->getCreatedAt(), 'short', true);
    }

    /**
     * History item expiration date getter
     *
     * @param Gri_Reward_Model_Reward_History $item
     * @return string
     */
    public function getExpirationDate(Gri_Reward_Model_Reward_History $item)
    {
        $expiresAt = $item->getExpiresAt();
        if ($expiresAt) {
            return Mage::helper('core')->formatDate($expiresAt, 'short', true);
        }
        return '';
    }

    /**
     * Return reword points update history collection by customer and website
     *
     * @return Gri_Reward_Model_Mysql4_Reward_History_Collection
     */
    protected function _getCollection()
    {
        if (!$this->_collection) {
            $websiteId = Mage::app()->getWebsite()->getId();
            $this->_collection = Mage::getModel('gri_reward/reward_history')->getCollection()
                ->addCustomerFilter(Mage::getSingleton('customer/session')->getCustomerId())
                ->addWebsiteFilter($websiteId)
                ->setExpiryConfig(Mage::helper('gri_reward')->getExpiryConfig())
                ->addExpirationDate($websiteId)
                ->skipExpiredDuplicates()
                ->setDefaultOrder()
            ;
        }
        return $this->_collection;
    }

    /**
     * Instantiate Pagination
     *
     * @return Gri_Reward_Block_Customer_Reward_History
     */
    protected function _prepareLayout()
    {
        if ($this->_isEnabled()) {
            $pager = $this->getLayout()->createBlock('page/html_pager', 'reward.history.pager')
                ->setCollection($this->_getCollection())->setIsOutputRequired(false)
            ;
            $this->setChild('pager', $pager);
        }
        return parent::_prepareLayout();
    }

    /**
     * Whether the history may show up
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->_isEnabled()) {
            return parent::_toHtml();
        }
        return '';
    }

    /**
     * Whether the history is supposed to be rendered
     *
     * @return bool
     */
    protected function _isEnabled()
    {
        return Mage::helper('gri_reward')->isEnabledOnFront()
            && Mage::helper('gri_reward')->getGeneralConfig('publish_history');
    }
}

