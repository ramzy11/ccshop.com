<?php

require_once ('app/code/core/Mage/Checkout/controllers/CartController.php');

class Gri_CheckoutCustom_CartController extends Mage_Checkout_CartController {

    /**
     * Shopping cart display action
     */
    public function indexAction()
    {
        // ajax
        $params = $this->getRequest()->getParams();
        if (isset($params['isAjax']) && $params['isAjax'] == 1) {
            $success = TRUE;
            $message = '';
            $cart = array( $params['itemId']=>array('qty' => $params['qty']) );
            $this->getRequest()->setParam('cart',$cart);
            try {
                $this->_updateShoppingCart();
                //$message = $this->__('The quantity of %s have been change to %s.',$params['productName'],$params['qty']);
                //$this->_getSession()->addSuccess($message);
            }catch (Exception $e) {
                $success = FALSE;
                $message = $e->getMessage();
                Mage::log("ItemId= ".$params['itemId']." Qty: ".$params['qty'].' Exception '.$e->getMessage(), 7, 'shopping-cart.log');
            }

            $response = array('success' => $success, 'message' => $message);
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
            return;
        }

        //cart
        $cart = $this->_getCart();
        if ($cart->getQuote()->getItemsCount()) {
            $cart->init();
            $cart->save();

            if (!$this->_getQuote()->validateMinimumAmount()) {
                $minimumAmount = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())
                    ->toCurrency(Mage::getStoreConfig('sales/minimum_order/amount'));

                $warning = Mage::getStoreConfig('sales/minimum_order/description')
                    ? Mage::getStoreConfig('sales/minimum_order/description')
                    : Mage::helper('checkout')->__('Minimum order amount is %s', $minimumAmount);

                $cart->getCheckoutSession()->addNotice($warning);
            }
        }

        // Compose array of messages to add
        $messages = array();
        foreach ($cart->getQuote()->getMessages() as $message) {
            if ($message) {
                // Escape HTML entities in quote message to prevent XSS
                $message->setCode(Mage::helper('core')->escapeHtml($message->getCode()));
                $messages[] = $message;
            }
        }
        $cart->getCheckoutSession()->addUniqueMessages($messages);

        /**
         * if customer enteres shopping cart we should mark quote
         * as modified bc he can has checkout page in another window.
         */
        $this->_getSession()->setCartWasUpdated(true);

        Varien_Profiler::start(__METHOD__ . 'cart_display');
        $this
            ->loadLayout()
            ->_initLayoutMessages('checkout/session')
            ->_initLayoutMessages('catalog/session')
            ->getLayout()->getBlock('head')->setTitle($this->__('Shopping Cart'));
        $this->renderLayout();
        Varien_Profiler::stop(__METHOD__ . 'cart_display');
    }

    /**
     * Add product to shopping cart action
     */
    public function addAction() {
        $cart = $this->_getCart();
        $params = $this->getRequest()->getParams();

        try {
            if (isset($params['qty'])) {
                $filter = new Zend_Filter_LocalizedToNormalized(array('locale' => Mage::app()->getLocale()->getLocaleCode()));
                $params['qty'] = $filter->filter($params['qty']);
            }

            $product = $this->_initProduct();
            $related = $this->getRequest()->getParam('related_product');
            
            /**
             * Check product availability
             */
            if (!$product) {
                $this->_goBack();
                return;
            }

            /**
             *  Check  preorder  product
             */
            if (!$this->addPreOrder($product)) {
                return;
            }

            $cart->addProduct($product, $params);
            if (!empty($related)) {
                $cart->addProductsByIds(explode(',', $related));
            }

            $cart->save();
            $this->_getSession()->setCartWasUpdated(true);

            /**
             * @todo remove wishlist observer processAddToCart
             */
            Mage::dispatchEvent('checkout_cart_add_product_complete', array('product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse())
            );

            if (!$this->_getSession()->getNoCartRedirect(true)) {
                if (!$cart->getQuote()->getHasError() && !isset($params['quick-buy'])) {
                    $message = $this->__('%s was added to your shopping cart.', Mage::helper('core')->escapeHtml($product->getName()));
                    $this->_getSession()->addSuccess($message);
                }
                // quick buy will redirect to checkout page, bypass the shopping cart page
                if (isset($params['quick-buy']) && $params['quick-buy'] == 1) {
                    $this->_redirect('checkout/onepage');
                } else {
                    $this->_goBack();
                }
            }
        } catch (Mage_Core_Exception $e) {
            if ($this->_getSession()->getUseNotice(true)) {
                $this->_getSession()->addNotice(Mage::helper('core')->escapeHtml($e->getMessage()));
            } else {
                $messages = array_unique(explode("\n", $e->getMessage()));
                foreach ($messages as $message) {
                    $this->_getSession()->addError(Mage::helper('core')->escapeHtml($message));
                }
            }
            $url = $this->_getSession()->getRedirectUrl(true);
            if ($url) {
                $this->getResponse()->setRedirect($url);
            } else {
                $this->_redirectReferer(Mage::helper('checkout/cart')->getCartUrl());
            }
        } catch (Exception $e) {
            $this->_getSession()->addException($e, $this->__('Cannot add the item to shopping cart.'));
            Mage::logException($e);
            $this->_goBack();
        }
    }

    public function shipToCountryAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     *  @param  $product
     *  
     */
    public function addPreOrder($product) {
        $preorder = Mage::getSingleton('gri_preorder/preorder');
        if (!$preorder->addto_cart($product)) {
            return false;
        }
        return true;
    }

}