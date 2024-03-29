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
 * @package     Gri_GiftCardAccount
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

class Gri_GiftCardAccount_Model_Total_Invoice_Giftcardaccount extends Mage_Sales_Model_Order_Invoice_Total_Abstract
{
    /**
     * Collect gift card account totals for invoice
     *
     * @param Mage_Sales_Model_Order_Invoice $invoice
     * @return Gri_GiftCardAccount_Model_Total_Invoice_Giftcardaccount
     */
    public function collect(Mage_Sales_Model_Order_Invoice $invoice)
    {
        $order = $invoice->getOrder();
        if ($order->getBaseGiftCardsAmount() && $order->getBaseGiftCardsInvoiced() != $order->getBaseGiftCardsAmount()) {
            $gcaLeft = $order->getBaseGiftCardsAmount() - $order->getBaseGiftCardsInvoiced();
            $used = 0;
            $baseUsed = 0;
            if ($gcaLeft >= $invoice->getBaseGrandTotal()) {
                $baseUsed = $invoice->getBaseGrandTotal();
                $used = $invoice->getGrandTotal();

                $invoice->setBaseGrandTotal(0);
                $invoice->setGrandTotal(0);
            } else {
                $baseUsed = $order->getBaseGiftCardsAmount() - $order->getBaseGiftCardsInvoiced();
                $used = $order->getGiftCardsAmount() - $order->getGiftCardsInvoiced();

                $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal()-$baseUsed);
                $invoice->setGrandTotal($invoice->getGrandTotal()-$used);
            }

            $invoice->setBaseGiftCardsAmount($baseUsed);
            $invoice->setGiftCardsAmount($used);
        }
        return $this;
    }
}
