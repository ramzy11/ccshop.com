<?php

class Gri_Sales_Helper_Data extends Mage_Core_Helper_Abstract
{
    const BLOCK_PRODUCT_PROMOTION_RULE = 'product_promotion_rule_block';
    const BLOCK_PRODUCT_OPTIONAL_PROMOTION = 'product_optional_promotion_block';
    const CONFIG_PATH_PAY_NOW_ENABLED = 'sales/pay_now/enabled';
    const CONFIG_PATH_REPAY_ENABLED = 'sales/repay/enabled';
    const CONFIG_PATH_CREDITMEMO_NOTIFY_BEFORE_CONFIRM = 'sales/creditmemo/notify_before_confirm';
    const CONFIG_PATH_CREDITMEMO_TWO_STEP = 'sales/creditmemo/two_step';
    const UPGRADE_SHIPPING_METHOD = 'upgrade_shipping_method';

    protected $_paymentUrls = array();

    public function addSalesToMenu($topMenu)
    {
        $tree = $topMenu->getTree();
        $salesData =  array(
            'name' => $this->__('Sales'),
            'id' => 'sales');
        $salesNode = new Varien_Data_Tree_Node($salesData, 'id', $tree, $topMenu);
        $topMenu->addChild($salesNode);
        $flashCategory = Mage::getModel('catalog/category')->loadByAttribute('url_key','flash-sales');
        if($flashCategory) {
            $flashData = array(
                'name' => $this->__('Flash Sale'),
                'id' => 'category-node-' . $flashCategory->getId(),
                'url' => Mage::helper('catalog/category')->getCategoryUrl($flashCategory),
            );
            $flashNode = new Varien_Data_Tree_Node($flashData, 'id', $tree, $salesNode);
            $salesNode->addChild($flashNode);
        }
    }

    public function displayCartRuleInProductView()
    {
        $layout = Mage::app()->getLayout();
        /* @var $block Gri_Sales_Block_Product_View_CartRule */
        $block = $layout->getBlock($name = 'product.view.cart.rule');
        $block or $block = $layout->createBlock(
            'gri_sales/product_view_cartRule',
            $name,
            array(
                'template' => 'sales/product/view/cart_rule.phtml',
                'cms_block_id' => self::BLOCK_PRODUCT_PROMOTION_RULE,
            )
        );
        return $block->toHtml();
    }

    public function displayOptionalPromotionInProductView()
    {
        $layout = Mage::app()->getLayout();
        /* @var $block Gri_Sales_Block_Product_View_CartRule */
        $block = $layout->getBlock($name = 'product.view.optional.promotion');
        $block or $block = $layout->createBlock(
            'gri_sales/product_view_cartRule',
            $name,
            array(
                'template' => 'sales/product/view/optional_promotion.phtml',
                'cms_block_id' => self::BLOCK_PRODUCT_OPTIONAL_PROMOTION,
            )
        );
        return $block->toHtml();
    }

    public function getPaymentUrl(Mage_Sales_Model_Order $order)
    {
        if (!Mage::getStoreConfig(self::CONFIG_PATH_REPAY_ENABLED)) return FALSE;
        if (!isset($this->_paymentUrls[$order->getId()])) {
            $this->_paymentUrls[$order->getId()] = '';
            if ($order->getStatus() == 'pending') {
                $quote = $this->getQuoteModel();
                $quote->load($order->getQuoteId());
                try {
                    if ($orderPlaceRedirectUrl = $quote->getPayment()->getOrderPlaceRedirectUrl()) {
                        $this->_paymentUrls[$order->getId()] = $orderPlaceRedirectUrl . 'increment_id/' . $order->getIncrementId() . '/';
                    }
                } catch (Exception $e) {
                    Mage::logException($e);
                }
            }
        }
        return $this->_paymentUrls[$order->getId()];
    }

    public function getPayNowUrl(Mage_Sales_Model_Order $order)
    {
        if ($order->getStatus() != 'pending_payment' || !Mage::getStoreConfig(self::CONFIG_PATH_PAY_NOW_ENABLED)) return FALSE;
        return Mage::getUrl('sales/griOrder/payNow', array('order_id' => $order->getId()));
    }

    /**
     * Get Mage_Sales_Model_Quote instance
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuoteModel()
    {
        return Mage::getModel('sales/quote');
    }

    public function isCreditmemoTwoStep()
    {
        return Mage::getStoreConfig(self::CONFIG_PATH_CREDITMEMO_TWO_STEP);
    }

    public function needNotifyBeforeConfirm()
    {
        return Mage::getStoreConfig(self::CONFIG_PATH_CREDITMEMO_NOTIFY_BEFORE_CONFIRM);
    }

    public function setDefaultOrderStatusForState($state, $status)
    {
        Mage::register('order_default_status_for_' . $state, $status, TRUE);
        return $this;
    }

    public function updateProductQtyOrdered($items, $operator = '+')
    {
        // Need Admin store to save product data
        $storeId = Mage::app()->getStore()->getId();
        Mage::app()->getStore()->setStoreId(Mage_Core_Model_App::ADMIN_STORE_ID);
        foreach ($items as $item) {
            $product = $item->getProduct();
            $product->getOrigData() or $product->load($product->getId());
            $qtyOrdered = (float)$product->getQtyOrdered();
            $qtyOrdered = $operator == '+' ? $qtyOrdered + $item->getQty() : $qtyOrdered - $item->getQty();
            $product->setQtyOrdered(1 * $qtyOrdered)->setBestSeller(1 * $product->getBestSeller());
            $product->save();
            Mage::app()->cleanCache(array('PRODUCT-' . $product->getId()));
        }
        // Restore current store
        Mage::app()->getStore()->setStoreId($storeId);
    }

    /**
     * @param Mage_Sales_Model_Order $order
     * @return Mage_Sales_Model_Order_Invoice | null
     */
    public function getInvoiceByOrder(Mage_Sales_Model_Order $order)
    {
        return  $order->getInvoiceCollection()->getFirstItem();
    }

    /**
     * Return back url for logged in and guest users
     *
     * @return string
     */
    public function getBackUrl()
    {
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            return Mage::getUrl('*/*/history');
        }
        return Mage::getUrl('*/*/form');
    }
}
