<?php
   class   Gri_Vip_Model_GiftCard  extends  Gri_GiftCard_Model_Giftcard {
       
      /**
       *  get  gift card balance 
       * 
       *  @param  string $card_no
       *  @param  string $customer_id
       * 
       *  @return float | 0   
       */  
      public  function getCardBalance($code){
         /* @var $Giftcardaccount  Gri_GiftCardAccount_Model_Giftcardaccount  */
         $Giftcardaccount = $this->_getGiftCardAccount($code);
         
         $today = date('Y-m-d');
         if( $Giftcardaccount->getData('code') && $Giftcardaccount->getData('status') == '1'  &&
             //$Giftcardaccount->getData('state') == '0'  &&
                $Giftcardaccount->getData('date_expires') >= $today && 
                  $Giftcardaccount->getData('balance') > 0 
            ){
             return  $Giftcardaccount->getData('balance') ;
         }
               
         return  null ;
      }
      
      /**
       *  update giftcard balance 
       *  
       *  @param  string $card_no
       *  @param  float $pay_money
       *  
       *  @return int 
       */
      public  function updateCardBalance($code){
          /* @var Mage_core_customer  */
          $customer = $this->_getCustomer();
          /* @var Gri_GiftCardAccount_Model_Giftcardaccount */
          $giftcardaccount = $this->_getGiftCardAccount($code);
          $total_money = $this->_getCartTotal();
          
          $balance = $this->getCardBalance($code) ;
          if($balance && $balance <= $total_money){
             $balance = 0;  
          }
          
          if($balance && $balance > $total_money){
             $balance = $balance - $total_money;
          }
          
          //update balance
          $giftcardaccount->setData('balance' , $balance);
          $giftcardaccount->save();
      }
      
      /**
       *  get  total  in  cart  
       *  
       *  @return  float  $total_money
       */
      protected function _getCartTotal(){
          // fetch  all  items          
          $cartItems = Mage::getSingleton('checkout/cart')->getItems();
          $total_money = 0.00;
          foreach($cartItems as $item){
            $total_money += $item->getData('row_total_incl_tax');        
          }
          
          // return 
          return  $total_money ;
      }
      
      /**
       *  retrive  giftcardaccount  model  instance
       * 
       *  @return   Gri_GiftCardAccount_Model_Giftcardaccount
       */
      protected function _getGiftCardAccount($code){
         return  Mage::getSingleton('gri_GiftCardAccount/giftcardaccount')->load($code,'code');          
      }
            
      /**
       *  retirve  customer entity
       *   
       *  @return Mage_core_model_customer
       */
      public function _getCustomer() {
        if ($this->getData('customer') === NULL) {
            $this->setData('customer', Mage::getSingleton('customer/session')->getCustomer());
        }
        return $this->getData('customer');
      }
      
      /**
       *   udpate  quote  gift cards
       *   
       *   @param  array $giftCard
       *    
       */
      public  function updateQuoteGiftCards($giftCard){
         $quote = Mage::getSingleton('sales/quote')->load($giftCard['entity_id']);
         if($quote->getEntityId()){
            isset($giftCard['gift_cards']) ? $order->setGiftCards($giftCard['gift_cards']) : '' ;   
            isset($giftCard['base_gift_cards_amount']) ? $order->setBaseGiftCardsAmount($giftCard['base_gift_cards_amount']) : '';
            isset($giftCard['gift_cards_amount']) ? $order->setGiftCardsAmount($giftCard['gift_cards_amount']) : '';
            isset($giftCard['gift_cards_amount_used']) ? $order->setGiftCardsAmount($giftCard['gift_cards_amount_used']) : '';
            isset($giftCard['base_gift_cards_amount_used']) ? $order->setGiftCardsAmount($giftCard['base_gift_cards_amount_used']) : '';
                          	
            /* todo */                                  
            $order->save();
         }             
      }        
   }