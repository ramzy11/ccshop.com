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

class Gri_CustomerBalance_Block_Adminhtml_Customer_Edit_Tab_Customerbalance_Balance_History extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        $this->setTemplate('gri/customerbalance/balance/history.phtml');
    }

    protected function _prepareLayout()
    {
        $this->setChild('grid', $this->getLayout()->createBlock('gri_customerbalance/adminhtml_customer_edit_tab_customerbalance_balance_history_grid', 'customer.balance.history.grid'));
        return parent::_prepareLayout();
    }
}
