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

class Gri_GiftCard_Model_Source_Status extends Mage_Core_Model_Abstract
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => Mage_Sales_Model_Order_Item::STATUS_PENDING,
                'label' => Mage::helper('gri_giftcard')->__('Ordered')
            ),
            array(
                'value' => Mage_Sales_Model_Order_Item::STATUS_INVOICED,
                'label' => Mage::helper('gri_giftcard')->__('Invoiced')
            )
        );
    }
}
