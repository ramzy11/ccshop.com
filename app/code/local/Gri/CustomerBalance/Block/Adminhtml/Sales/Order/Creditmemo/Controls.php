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
 * @package     Gri_CustomerBalance
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Refund to customer balance functionality block
 *
 */
class Gri_CustomerBalance_Block_Adminhtml_Sales_Order_Creditmemo_Controls
    extends Mage_Core_Block_Template
{
    /**
     * Check whether refund to customerbalance is available
     *
     * @return bool
     */
    public function canRefundToCustomerBalance()
    {
        if ($this->getCreditmemo()->getOrder()->getCustomerIsGuest()) {
            return FALSE;
        }
        return TRUE;
    }

    /**
     * Check whether real amount can be refunded to customer balance
     *
     * @return bool
     */
    public function canRefundMoneyToCustomerBalance()
    {
        if (!$this->getCreditmemo()->getGrandTotal()) {
            return FALSE;
        }

        if ($this->getCreditmemo()->getOrder()->getCustomerIsGuest()) {
            return FALSE;
        }
        return TRUE;
    }

    /**
     * @return Gri_Sales_Model_Order_Creditmemo
     */
    public function getCreditmemo()
    {
        return Mage::registry('current_creditmemo');
    }

    /**
     * Prepopulate amount to be refunded to customerbalance
     *
     * @return float
     */
    public function getReturnValue()
    {
        $max = $this->getCreditmemo()->getBaseCustomerBalanceReturnMax();
        if ($max) {
            return $max;
        }
        return 0;
    }
}
