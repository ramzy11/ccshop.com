<?php

class Gri_Wishlist_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Retrieve url for adding product to wishlist
     *
     * @param int $itemId
     *
     * @return  string
     */
    public function getMoveFromCartUrl($itemId)
    {
        $request = Mage::app()->getRequest();
        $port = $request->getServer('SERVER_PORT');
        $secure = $port == 443 ? TRUE : FALSE ;
        return $this->_getUrl('wishlist/index/fromcart', array('item' => $itemId, '_secure' => $secure));
    }
}