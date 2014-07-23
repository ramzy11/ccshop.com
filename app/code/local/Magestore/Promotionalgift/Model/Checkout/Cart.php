<?php


class Magestore_Promotionalgift_Model_Checkout_Cart extends Mage_Checkout_Model_Cart
{

    /**
     * Update cart items information
     *
     * @param   array $data
     * @return  Mage_Checkout_Model_Cart
     */
    public function updateItems($data)
    {
		if(Mage::helper('promotionalgift')->enablePromotionalgift()){
			$upgrades = array();
			foreach($data as $itemId => $itemInfo){
				$item = $this->getQuote()->getItemById($itemId);
				$next = '';
				if($item){
					$itemOptions = $item->getOptions();
					foreach($itemOptions as $option){
						$oData = $option->getData();
						if($oData['code']=='option_promotionalgift_catalogrule'){
							$next = $oData['item_id'];
						}
						if($next == $itemId) continue;
					}
				}else{
					continue;
				}
				if($next == $itemId) continue;
				$newQty = $itemInfo['qty'];
				$oldQty = $item->getQty();
				if($newQty != $oldQty){
					$qtyUpdate = $newQty - $oldQty;
					$productId = $item->getProductId();
					$availableRule = Mage::getModel('promotionalgift/catalogrule')->validateItem($productId);
					if($availableRule){
						$catalog_rule_id = $availableRule->getId();
						$quoteId = Mage::getModel('checkout/cart')->getQuote()->getId();
						$quotes = Mage::getModel('promotionalgift/quote')
									->getCollection()
									->addFieldToFilter('quote_id',$quoteId)
									->addFieldToFilter('catalog_rule_id',$catalog_rule_id)
									;
						$itemIds = array();			
						foreach($quotes as $quote){
							if(!in_array($quote->getItemId(),$itemIds)){
								$itemIds[] = $quote->getItemId();
								$number_item_free = $quote->getData('number_item_free');
								$upgrades[$quote->getItemId()] += $qtyUpdate * $number_item_free;
							}
						}
					}
				}
			}
			if($upgrades){
				foreach($upgrades as $upgrade=>$key){
					if($data[$upgrade])
						$data[$upgrade] = array(
							'qty' => $data[$upgrade]['qty'] + $key,
							'before_suggest_qty' => $data[$upgrade]['qty'] + $key,
						);
				}
			}
		}
        Mage::dispatchEvent('checkout_cart_update_items_before', array('cart'=>$this, 'info'=>$data));

        /* @var $messageFactory Mage_Core_Model_Message */
        $messageFactory = Mage::getSingleton('core/message');
        $session = $this->getCheckoutSession();
        $qtyRecalculatedFlag = false;
        foreach ($data as $itemId => $itemInfo) {
            $item = $this->getQuote()->getItemById($itemId);
            if (!$item) {
                continue;
            }

            if (!empty($itemInfo['remove']) || (isset($itemInfo['qty']) && floatval($itemInfo['qty'])<=0)) {
				$this->getQuote()->removeItem($itemId);
                continue;
            }

            $qty = isset($itemInfo['qty']) ? (float) $itemInfo['qty'] : false;
            if ($qty > 0) {
                $item->setQty($qty);
                if ($item->getHasError()) {
                    Mage::throwException($item->getMessage());
                }

                if (isset($itemInfo['before_suggest_qty']) && ($itemInfo['before_suggest_qty'] != $qty)) {
                    $qtyRecalculatedFlag = true;
                    $message = $messageFactory->notice(Mage::helper('checkout')->__('Quantity was recalculated from %d to %d', $itemInfo['before_suggest_qty'], $qty));
                    $session->addQuoteItemMessage($item->getId(), $message);
                }
            }
        }

        if ($qtyRecalculatedFlag) {
            $session->addNotice(
                Mage::helper('checkout')->__('Some products quantities were recalculated because of quantity increment mismatch')
            );
        }

        Mage::dispatchEvent('checkout_cart_update_items_after', array('cart'=>$this, 'info'=>$data));
        return $this;
    }
	
	public function removeItem($itemId)
    {
		if(!Mage::helper('promotionalgift')->enablePromotionalgift()){
			$this->getQuote()->removeItem($itemId);
			return $this;
		}
		$quoteId = Mage::getModel('checkout/cart')->getQuote()->getId();
		$catalogQuotes = Mage::getModel('promotionalgift/quote')
								->getCollection()
								->addFieldToFilter('item_id',$itemId)
								->addFieldToFilter('quote_id',$quoteId);
		if(count($catalogQuotes) > 0){
			foreach($catalogQuotes as $catalogQuote){
				$catalogQuote->delete();
			}
			$this->getQuote()->removeItem($itemId);
			return $this;
		}
		$shoppingQuotes = Mage::getModel('promotionalgift/shoppingquote')
								->getCollection()
								->addFieldToFilter('item_id',$itemId)
								->addFieldToFilter('quote_id',$quoteId);
		if(count($shoppingQuotes) > 0){
			foreach($shoppingQuotes as $shoppingQuote){
				$shoppingQuote->delete();
			}
			$this->getQuote()->removeItem($itemId);
			return $this;
		}
		$item = $this->getQuote()->getItemById($itemId);
		if($item){
			$productId = $item->getProductId();
			$availableRule = Mage::getModel('promotionalgift/catalogrule')->validateItem($productId);
			if($availableRule){
				$cartItems = Mage::getModel('checkout/cart')->getItems();
				$data = array();
				foreach($cartItems as $cartItem){
					if($cartItem->getData('parent_item_id')) continue;
					$qty = $cartItem->getQty();
					if($cartItem->getId() == $itemId) $qty = 0;
					$data[$cartItem->getId()] = array(
						"qty" => $qty,
						"before_suggest_qty" => $qty,
					);
				}
				if($data){
					$this->updateItems($data);
				}
			}else{
				$this->getQuote()->removeItem($itemId);
			}
		}
        return $this;
    }
	
	public function updateItem($itemId, $requestInfo = null, $updatingParams = null)
    {
		if(Mage::helper('promotionalgift')->enablePromotionalgift()){
			$quoteId = Mage::getModel('checkout/cart')->getQuote()->getId();
			$productId = Mage::getModel('sales/quote_item')->load($itemId, 'item_id')->getProductId();
			$catalogQuotes = Mage::getModel('promotionalgift/quote')
								->getCollection()
								->addFieldToFilter('item_id',$itemId)
								->addFieldToFilter('quote_id',$quoteId)
								->getFirstItem();
			$shoppingQuotes = Mage::getModel('promotionalgift/shoppingquote')
								->getCollection()
								->addFieldToFilter('item_id',$itemId)
								->addFieldToFilter('quote_id',$quoteId)
								->getFirstItem();;
			if($catalogQuotes->getId() || $shoppingQuotes->getId()){
				if($catalogQuotes->getId()){
					$catalogRuleId = $catalogQuotes->getCatalogRuleId();
					$freeGiftItemQty = $catalogQuotes->getNumberItemFree();
					Mage::getModel('checkout/session')->setData('catalog_rule_id', $catalogRuleId);
					Mage::getModel('checkout/session')->setData('free_gift_item_qty', $freeGiftItemQty);
					if($productId)
						Mage::getModel('checkout/session')->setData('free_gift_item',$productId);							
				}
				if($shoppingQuotes->getId()){
					$shoppingCartRuleId = $shoppingQuotes->getShoppingcartruleId();		
					Mage::getModel('checkout/session')->setData('shoppingcart_rule_id', $shoppingCartRuleId);	
					if($productId)
						Mage::getModel('checkout/session')->setData('shoppingcart_gift_item', $productId);											
				}	
				return $this->updateGiftItem($itemId, $requestInfo, $updatingParams);
			}
		}
        try {
            $item = $this->getQuote()->getItemById($itemId);
            if (!$item) {
                Mage::throwException(Mage::helper('checkout')->__('Quote item does not exist.'));
            }
            $productId = $item->getProduct()->getId();
            $product = $this->_getProduct($productId);
            $request = $this->_getProductRequest($requestInfo);
            if ($product->getStockItem()) {
                $minimumQty = $product->getStockItem()->getMinSaleQty();
                // If product was not found in cart and there is set minimal qty for it
                if ($minimumQty && ($minimumQty > 0)
                    && ($request->getQty() < $minimumQty)
                    && !$this->getQuote()->hasProductId($productId)
                ) {
                    $request->setQty($minimumQty);
                }
            }
			if(Mage::helper('promotionalgift')->enablePromotionalgift()){
				if($item->getQty() != $request['qty']){
					$upgrades = array();
					$productId = $item->getProductId();
					$availableRule = Mage::getModel('promotionalgift/catalogrule')->validateItem($productId);
					if($availableRule){
						$cartItems = Mage::getModel('checkout/cart')->getItems();
						$data = array();
						foreach($cartItems as $cartItem){
							$qty = $cartItem->getQty();
							if($cartItem->getId() == $itemId) $qty = $request['qty'];
							$data[$cartItem->getId()] = array(
								"qty" => $qty,
								"before_suggest_qty" => $qty,
							);
						}
						if($data){
							$this->updateItems($data);
						}
					}
				}
			}
            $result = $this->getQuote()->updateItem($itemId, $request, $updatingParams);
        } catch (Mage_Core_Exception $e) {
            $this->getCheckoutSession()->setUseNotice(false);
            $result = $e->getMessage();
        }

        /**
         * We can get string if updating process had some errors
         */
        if (is_string($result)) {
            if ($this->getCheckoutSession()->getUseNotice() === null) {
                $this->getCheckoutSession()->setUseNotice(true);
            }
            Mage::throwException($result);
        }

        Mage::dispatchEvent('checkout_cart_product_update_after', array(
            'quote_item' => $result,
            'product' => $product
        ));
        $this->getCheckoutSession()->setLastAddedProductId($productId);
        return $result;
    }
	
	public function updateGiftItem($itemId, $requestInfo = null, $updatingParams = null)
    {
        try {
            $item = $this->getQuote()->getItemById($itemId);
            if (!$item) {
                Mage::throwException(Mage::helper('checkout')->__('Quote item does not exist.'));
            }
            $productId = $item->getProduct()->getId();
            $product = $this->_getProduct($productId);
            $request = $this->_getProductRequest($requestInfo);
            if ($product->getStockItem()) {
                $minimumQty = $product->getStockItem()->getMinSaleQty();
                // If product was not found in cart and there is set minimal qty for it
                if ($minimumQty && ($minimumQty > 0)
                    && ($request->getQty() < $minimumQty)
                    && !$this->getQuote()->hasProductId($productId)
                ) {
                    $request->setQty($minimumQty);
                }
            }
			if(Mage::helper('promotionalgift')->enablePromotionalgift()){
				if($item->getQty() != $request['qty']){
					$next = '';
					$itemOptions = $item->getOptions();
					$quotes = Mage::getModel('promotionalgift/quote')
									->getCollection()
									->addFieldToFilter('quote_id',$this->getQuote()->getId())
									->addFieldToFilter('item_id',$itemId)
									;
					$shoppingQuotes = Mage::getModel('promotionalgift/shoppingquote')
									->getCollection()
									->addFieldToFilter('quote_id',$this->getQuote()->getId())
									->addFieldToFilter('item_id',$itemId)
									;
					if((count($quotes)>0) || (count($shoppingQuotes)>0))
						$next = 1;
					if($next == 1){
						$request['qty'] = $item->getQty();
					}else{
						$upgrades = array();
						$productId = $item->getProductId();
						$availableRule = Mage::getModel('promotionalgift/catalogrule')->validateItem($productId);
						if($availableRule){
							$cartItems = Mage::getModel('checkout/cart')->getItems();
							$data = array();
							foreach($cartItems as $cartItem){
								$qty = $cartItem->getQty();
								if($cartItem->getId() == $itemId) $qty = $request['qty'];
								$data[$cartItem->getId()] = array(
									"qty" => $qty,
									"before_suggest_qty" => $qty,
								);
							}
							if($data){
								$this->updateItems($data);
							}
						}
					}
				}
			}
            $result = $this->getQuote()->updateItem($itemId, $request, $updatingParams);
        } catch (Mage_Core_Exception $e) {
            $this->getCheckoutSession()->setUseNotice(false);
            $result = $e->getMessage();
        }

        /**
         * We can get string if updating process had some errors
         */
        if (is_string($result)) {
            if ($this->getCheckoutSession()->getUseNotice() === null) {
                $this->getCheckoutSession()->setUseNotice(true);
            }
            Mage::throwException($result);
        }

        Mage::dispatchEvent('checkout_cart_product_update_after', array(
            'quote_item' => $result,
            'product' => $product
        ));
        $this->getCheckoutSession()->setLastAddedProductId($productId);
        return $result;
    }
}
